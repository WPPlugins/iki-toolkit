<?php

/**
 * Handles iteration of grid elements (assets not posts ) via simple iterator
 */
class Iki_Asset_Grid extends Iki_Grid {

	/**@var ArrayIterator $iterator */
	protected $iterator;
	protected $type = 'asset';
	protected $currentItem;


	/**
	 * @param $aggregate
	 */
	protected function setup_aggregate( $aggregate ) {
		parent::setup_aggregate( $aggregate );
		$this->iterator = new ArrayIterator( $this->aggregate );
	}

	/**
	 * @return bool|mixed
	 */
	protected function item_iterator() {

		if ( $this->iterator->valid() ) {

			$this->currentItem = $this->iterator->current();

			$this->iterator->next();

			return $this->currentItem;

		} else {
			return false;
		}

	}

	/**
	 * @param $cell
	 */
	protected function modify_cell_data( $cell ) {

		$cell->data['asset_id'] = $this->currentItem;
	}

	protected function get_total_cells() {
		return $this->iterator->count();
	}
}

