<div class="searchbar-wrap">
	<?php $search_suggestions = function_exists( 'msrproducts_search_suggestions' ) ? msrproducts_search_suggestions( 12 ) : array(); ?>
	<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="input-group">
			<label class="screen-reader-text" for="s"><?php echo esc_html_x( 'Search for:', 'label', 'msrproducts' ); ?></label>
			<input class="form-control" size="20" type="search" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="s" placeholder="<?php echo esc_attr( msrproducts_get_catalog_search_placeholder() ); ?>" list="catalog-search-suggestions" autocomplete="off" />
			<button class="btn btn-success" type="submit" id="searchsubmit">Search</button>
		</div>
	</form>
	<datalist id="catalog-search-suggestions">
		<?php foreach ( $search_suggestions as $suggestion ) : ?>
			<option value="<?php echo esc_attr( $suggestion ); ?>"></option>
		<?php endforeach; ?>
	</datalist>
</div>