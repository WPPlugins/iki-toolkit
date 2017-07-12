<?php

interface Iki_IGrid {
	public function print_grid( $aggreate, $offset, $use_grid_wrapper );
}

/**
 * Handles printing of the grid.
 */
abstract class Iki_Grid implements Iki_IGrid {

	protected $type;
	/**@var $current_cell Iki_Grid_Cell */
	protected $current_cell = null;

	protected $grid_rows;

	protected $aggregate;
	protected $default_row;

	protected $id = null;
	protected $use_grid_wrapper = true;
	protected $fill_grid = true;

	/**@var Iki_Row $active_row */
	protected $active_row;
	protected $grid_location;

	protected $extra_data;
	protected $suppress_row_hooks = true;
	protected $cell_offset = 0;

	protected $cached_cell_partial;

	/**
	 * Iki_Grid constructor.
	 *
	 * @param $grid_rows
	 * @param null $default_row
	 * @param null $id
	 * @param bool $use_grid_wrapper
	 * @param bool $fill_grid
	 * @param string $grid_location
	 */
	public function __construct( $grid_rows, $default_row = null, $id = null, $use_grid_wrapper = true, $fill_grid = true, $grid_location = '' ) {

		$this->use_grid_wrapper = true;
		$this->fill_grid        = $fill_grid;
		$this->id               = $id;
		$this->grid_rows        = $grid_rows;
		$this->default_row      = new Iki_Grid_Row_Data( $default_row );
		$this->grid_location    = $grid_location;
	}

	/** Suppress grid hooks
	 *
	 * @param $suppress
	 */
	public function set_supress_row_hooks( $suppress ) {
		$this->suppress_row_hooks = $suppress;
	}

	/**
	 * Return grid type (post,asset)
	 * @return string grid type
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @return null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return Iki_Row
	 */
	public function get_current_row() {
		return $this->active_row;
	}

	/**
	 * @return string
	 */
	public function get_location() {
		return $this->grid_location;
	}


	/**
	 * @param $aggreate
	 */
	protected function setup_aggregate( $aggreate ) {
		$this->aggregate = $aggreate;

	}

	/**
	 * @return mixed
	 */
	public function get_data() {
		return $this->extra_data;
	}

	/**
	 * @return bool
	 */
	public function is_using_grid_wrapper() {
		return $this->use_grid_wrapper;
	}

	/**
	 * @param $aggreate
	 * @param null $offset
	 * @param bool $use_grid_wrapper
	 * @param int $break_after
	 *
	 * @return int
	 */
	public function print_grid( $aggreate, $offset = null, $use_grid_wrapper = true, $break_after = 0 ) {
		$this->use_grid_wrapper = $use_grid_wrapper;

		Iki_Grids::get_instance()->set_active_grid( $this );
		$this->extra_data                     = apply_filters( 'iki_grid_setup_extra_data', array(), $this );
		$this->extra_data['id']               = $this->id;
		$this->extra_data['use_grid_wrapper'] = $this->use_grid_wrapper;

		$this->setup_aggregate( $aggreate );

		$printed_cells = $this->_print_grid( $offset, $break_after );
		Iki_Grids::get_instance()->set_active_grid( null );

		return $printed_cells;
	}

	/**
	 * @param null $offset
	 * @param $break_after
	 *
	 * @return int
	 */
	protected function _print_grid( $offset = null, $break_after ) {
		if ( $this->use_grid_wrapper ) {
			do_action( 'iki_grid_before', $this );
			$this->open_grid_wrapper();
		}
		$printed_cells = $this->print_rows( $offset, $break_after );
		wp_reset_query();
		wp_reset_postdata();
		if ( $this->use_grid_wrapper ) {
			$this->close_grid_wrapper();
			do_action( 'iki_grid_after', $this );
		}

		return $printed_cells;
	}


	/**
	 * @param null $offset
	 * @param int $break_after
	 *
	 * @return int
	 */
	protected function print_rows( $offset = null, $break_after = 0 ) {


		$current_row_number   = 1;
		$row_iterator_offset  = 0;
		$cell_iterator_offset = 0;

		if ( ! is_null( $offset ) ) {
			$row_iterator_offset  = $offset['row_start_num'];
			$cell_iterator_offset = $offset['cell_start_num'];
			$current_row_number   = $row_iterator_offset;
		}


		$cell_iterator = 0;
		$total_rows    = count( $this->grid_rows );

		//calculation for a "looping" grid. So the rows are being printed as if grid is
		// being continously scanned from top to bottom
		if ( $row_iterator_offset && $row_iterator_offset > $total_rows ) {
			$remainder = $row_iterator_offset % $total_rows;
			$remainder = $remainder - 1;
			if ( $remainder < 0 ) {
				$remainder = $total_rows - 1;
			}
			$row_iterator = $remainder;
		} else {
			$row_iterator = $row_iterator_offset - 1;
		}

		//failsafe check
		if ( ! isset( $this->grid_rows[ $row_iterator ] ) ) {
			$row_iterator = 0;
		}

		$total_cells = $this->get_total_cells();

		/**@var Iki_Grid_Data $grid_data */
		$grid_data = new Iki_Grid_Data( array(
			'cell_iterator'        => $cell_iterator,
			'cell_iterator_offset' => $cell_iterator_offset,
			'fill_grid'            => $this->fill_grid,
			'total_cells'          => $total_cells,
			'current_row_num'      => $current_row_number
		) );


		/**@var Iki_Grid_Row_Data $current_row_data */
		$current_row_data = $this->grid_rows[ $row_iterator ];
		$this->active_row = Iki_Row_Factory::get_row( $this, $current_row_data, $grid_data );

		if ( ! $this->suppress_row_hooks ) {
			do_action( 'iki_grid_row_before', $this );
		}
		echo $this->active_row->open();

		$this->loop_start();

		while ( $this->item_iterator() ) {

			//force end grid ( used in ajax calls to print only missing cells
			if ( $break_after !== 0 && $cell_iterator >= $break_after ) {
				break 1;
			}
			$this->loop_iteration_start();

			//no more empty cells - new row generation
			if ( ! $this->active_row->has_empty_cells() ) {

				echo $this->active_row->close();
				if ( ! $this->suppress_row_hooks ) {

					do_action( 'iki_grid_row_after', $this );
				}

				$row_iterator ++;
				$current_row_number ++;
				if ( ! isset( $this->grid_rows[ $row_iterator ] ) ) {
					$row_iterator = 0;
				}

				/**@var Iki_Grid_Data $gridData */
				$grid_data = new Iki_Grid_Data( array(
					'cell_iterator'        => $cell_iterator,
					'cell_iterator_offset' => $cell_iterator_offset,
					'fill_grid'            => $this->fill_grid,
					'total_cells'          => $total_cells,
					'current_row_num'      => $current_row_number
				) );

				/**@var $currentRow Iki_Grid_Row_Data */
				$current_row_data = $this->grid_rows[ $row_iterator ];
				$this->active_row = Iki_Row_Factory::get_row( $this, $current_row_data, $grid_data );

				if ( ! $this->suppress_row_hooks ) {
					do_action( 'iki_grid_row_before' );
				}
				echo $this->active_row->open();
			}

			$activeCell = $this->active_row->prepare_cell();
			$this->modify_cell_data( $activeCell );
			$this->active_row->print_cell();

			$cell_iterator ++;

			$this->loop_iteration_end();
		} // end while have_posts()

		$this->loop_end();
		if ( $this->active_row->is_open ) { //close the last grid
			echo $this->active_row->close();
			if ( ! $this->suppress_row_hooks ) {
				do_action( 'iki_grid_row_after' );
			}
		}

		return $cell_iterator;

	}


	abstract protected function item_iterator();

	abstract protected function get_total_cells();

	public function get_item_template() {
		if ( $this->cached_cell_partial ) {

			get_template_part( $this->cached_cell_partial['slug'], $this->cached_cell_partial['name'] );

		} else {

			$grid_cell_template = array(
				'slug' => '',
				'name' => ''
			);

			$grid_cell_template = apply_filters( 'iki_grid_cell_partial', $grid_cell_template, $this );

			if ( ! empty( $grid_cell_template['slug'] ) ) {
				get_template_part( $grid_cell_template['slug'], $grid_cell_template['name'] );
				$this->cached_cell_partial = array(
					'slug' => $grid_cell_template['slug'],
					'name' => $grid_cell_template['name']
				);
			}
		}
	}

	/**
	 * Offset the calculation of total cells
	 * This is done for wordpress query, because we can't modify the query directly.
	 *
	 * @param $cell_offset
	 */
	public function set_cell_offset( $cell_offset ) {
		$this->cell_offset = $cell_offset;
	}

	protected function loop_start() {
		//noop
	}

	protected function loop_iteration_start() {
		//noop
	}

	protected function loop_end() {
		//noop
	}

	protected function loop_iteration_end() {
		//noop
	}

	protected function modify_cell_data( $cell ) {
		//noop
	}

	protected function open_grid_wrapper() {

		$classes = array( 'iki-grid-wrapper' );

		$gridData  = apply_filters( 'iki_grid_data_js', array(), $this );
		$classes   = apply_filters( 'iki_grid_class', $classes, $this );
		$grid_id   = 'iki-grid-' . $this->get_location();
		$classes[] = 'iki-grid-id-' . $this->get_id();
		$grid_json = '';
		if ( ! empty( $gridData ) ) {
			$valid_json = json_encode( $gridData );
			if ( $valid_json ) {
				$grid_json = "data-iki-layout='" . $valid_json . "'";
			}
		};


		//recursively sanitize html class attribute (sanitize_html_class)
		$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );
		do_action( 'iki_grid_wrapper_open_before', $this );
		printf( '<div id="%3$s" class="%1$s" %2$s >', $classes, $grid_json, esc_attr( $grid_id ) );
		do_action( 'iki_grid_wrapper_open_after', $this );

	}

	protected function close_grid_wrapper() {

		do_action( 'iki_grid_wrapper_close_before', $this );
		echo '</div>';
		do_action( 'iki_grid_wrapper_close_after', $this );

	}

	public function get_current_cell() {
		return $this->active_row->get_current_cell();
	}

}

