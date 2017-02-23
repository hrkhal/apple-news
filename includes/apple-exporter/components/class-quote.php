<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\Components\Quote class
 *
 * Contains a class which is used to transform blockquotes into Apple News format.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 0.2.0
 */

namespace Apple_Exporter\Components;

use \DOMElement;

/**
 * A class which is used to transform blockquotes into Apple News format.
 *
 * @since 0.2.0
 */
class Quote extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param DOMElement $node The node to examine.
	 *
	 * @access public
	 * @return DOMElement|null The DOMElement on match, false on no match.
	 */
	public static function node_matches( $node ) {
		return ( 'blockquote' === $node->nodeName ) ? $node : null;
	}

	/**
	 * Register all specs for the component.
	 *
	 * @param string $text
	 * @access protected
	 */
	public function register_specs() {
		$this->register_spec(
			'blockquote-without-border-json',
			__( 'Blockquote Without Border JSON', 'apple-news' ),
			array(
				'role' => 'container',
				'layout' => array(
					'columnStart' => '%%body_offset%%',
					'columnSpan' => '%%body_column_span%%',
					'margin' => array(
						'bottom' => '%%layout_gutter%%',
						'top' => '%%layout_gutter%%',
					),
				),
				'style' => array(
					'backgroundColor' => '%%blockquote_background_color%%',
				),
				'components' => array(
					array(
						'role' => 'quote',
						'text' => '%%text%%',
						'format' => '%%format%%',
						'layout' => 'blockquote-layout',
						'textStyle' => 'default-blockquote',
					)
				),
			)
		);

		$this->register_spec(
			'blockquote-with-border-json',
			__( 'Blockquote With Border JSON', 'apple-news' ),
			array(
				'role' => 'container',
				'layout' => array(
					'columnStart' => '%%body_offset%%',
					'columnSpan' => '%%body_column_span%%',
					'margin' => array(
						'bottom' => '%%layout_gutter%%',
						'top' => '%%layout_gutter%%',
					),
				),
				'style' => array(
					'backgroundColor' => '%%blockquote_background_color%%',
					'border' => array(
						'all' => array(
							'width' => '%%blockquote_border_width%%' ),
							'style' => '%%blockquote_border_style%%',
							'color' => '%%blockquote_border_color%%',
						),
						'bottom' => false,
						'right' => false,
						'top' => false,
					)
				),
				'components' => array(
					array(
						'role' => 'quote',
						'text' => '%%text%%',
						'format' => '%%format%%',
						'layout' => 'blockquote-layout',
						'textStyle' => 'default-blockquote',
					)
				),
			)
		);

		$this->register_spec(
			'blockquote-layout',
			__( 'Blockquote Layout', 'apple-news' ),
			array(
				'contentInset' => array(
					'bottom' => true,
					'left' => true,
					'right' => true,
					'top' => true,
				),
			)
		);

		$this->register_spec(
			'default-blockquote',
			__( 'Blockquote Style', 'apple-news' ),
			array(
				'fontName' => '%%blockquote_font%%',
				'fontSize' => '%%blockquote_size%%',
				'textColor' => '%%blockquote_color%%',
				'lineHeight' => '%%blockquote_line_height%%',
				'textAlignment' => '%%textAlignment%%',
				'tracking' => '%%blockquote_tracking%%',
			)
		);

		$this->register_spec(
			'pullquote-without-border-json',
			__( 'Pull quote Without Border JSON', 'apple-news' ),
			array(
				'role' => 'container',
				'layout' => array(
					'columnStart' => 3,
					'columnSpan' => 4
				),
				'components' => array(
					array(
						'role' => 'quote',
						'text' => '%%text%%',
						'format' => '%%format%%',
						'layout' => 'pullquote-layout',
						'textStyle' => 'default-pullquote',
					)
				),
				'anchor' => array(
					'targetComponentIdentifier' => 'pullquoteAnchor',
					'originAnchorPosition' => 'top',
					'targetAnchorPosition' => 'top',
					'rangeStart' => 0,
					'rangeLength' => 10,
				),
			)
		);

		$this->register_spec(
			'pullquote-with-border-json',
			__( 'Pull quote With Border JSON', 'apple-news' ),
			array(
				'role' => 'container',
				'layout' => array(
					'columnStart' => 3,
					'columnSpan' => 4
				),
				'components' => array(
					array(
						'role' => 'quote',
						'text' => '%%text%%',
						'format' => '%%format%%',
						'layout' => 'pullquote-layout',
						'textStyle' => 'default-pullquote',
					)
				),
				'style' => array(
					'border' => array(
						'all' => array (
							'width' => '%%pullquote_border_width%%',
							'style' => '%%pullquote_border_style%%',
							'color' => '%%pullquote_border_color%%',
						),
						'left' => false,
						'right' => false,
					)
				),
				'anchor' => array(
					'targetComponentIdentifier' => 'pullquoteAnchor',
					'originAnchorPosition' => 'top',
					'targetAnchorPosition' => 'top',
					'rangeStart' => 0,
					'rangeLength' => 10,
				),
			)
		);

		$this->register_spec(
			'pullquote-layout',
			__( 'Pull quote Layout', 'apple-news' ),
			array(
				'margin' => array(
					'top' => 12,
					'bottom' => 12,
				),
			)
		);

		$this->register_spec(
			'default-pullquote',
			__( 'Pull quote Style', 'apple-news' ),
			array(
				'fontName' => '%%pullquote_font%%',
				'fontSize' => '%%pullquote_size%%',
				'textColor' => '%%pullquote_color%%',
				'textTransform' => '%%pullquote_transform%%',
				'lineHeight' => '%%pullquote_line_height%%',
				'textAlignment' => '%%textAlignment%%',
				'tracking' => '%%pullquote_tracking%%',
			)
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 *
	 * @access protected
	 */
	protected function build( $html ) {

		// Extract text from blockquote HTML.
		preg_match( '#<blockquote.*?>(.*?)</blockquote>#si', $html, $matches );
		$text = $matches[1];

		// Split for pullquote vs. blockquote.
		if ( 0 === strpos( $html, '<blockquote class="apple-news-pullquote">' ) ) {
			$this->_build_pullquote( $text );
		} else {
			$this->_build_blockquote( $text );
		}
	}

	/**
	 * Processes given text to apply smart quotes on either end of provided text.
	 *
	 * @param string $text The text to process.
	 *
	 * @access private
	 * @return string The modified text.
	 */
	private function _apply_hanging_punctuation( $text ) {

		// Trim the fat before beginning.
		$text = trim( $text );

		// Strip any double quotes already present.
		$modified_text = trim( $text, '"“”' );

		// Add smart quotes around the text.
		$modified_text = '“' . $modified_text . '”';

		/**
		 * Allows for modification of a quote with hanging punctuation enabled.
		 *
		 * @since 1.2.4
		 *
		 * @param string $modified_text The modified text to be filtered.
		 * @param string $text The original text for the quote.
		 */
		$modified_text = apply_filters(
			'apple_news_apply_hanging_punctuation',
			$modified_text,
			$text
		);

		// Re-add the line breaks.
		$modified_text .= "\n\n";

		return $modified_text;
	}

	/**
	 * Runs the build operation for a blockquote.
	 *
	 * @param string $text The text to use when building the blockquote.
	 *
	 * @access private
	 */
	private function _build_blockquote( $text ) {

		// Set JSON for this element.
		$values = array(
			'layout' => array(
				'columnStart' => $this->get_setting( 'body_offset' ),
				'columnSpan' => $this->get_setting( 'body_column_span' ),
				'margin' => array(
					'bottom' => $this->get_setting( 'layout_gutter' ),
					'top' => $this->get_setting( 'layout_gutter' ),
				),
			),
			'style' => array(
				'backgroundColor' => $this->get_setting( 'blockquote_background_color' ),
			),
			'components' => array(
				array(
					'text' => $this->parser->parse( $text ),
					'format' => $this->parser->format,
				)
			),
		);

		// Set component attributes.
		// Determine if there is a border specified.
		if ( 'none' !== $this->get_setting( 'blockquote_border_style' ) ) {
			$values = $this->_set_blockquote_border( $values );
			$spec_name = 'blockquote-with-border-json';
		} else {
			$spec_name = 'blockquote-without-border-json';
		}

		$this->register_json( $spec_name, $values );

		$this->_set_blockquote_layout();
		$this->_set_blockquote_style();
	}

	/**
	 * Runs the build operation for a pullquote.
	 *
	 * @param string $text The text to use when building the pullquote.
	 *
	 * @access private
	 */
	private function _build_pullquote( $text ) {

		// Apply additional formatting to the text if hanging punctuation is set.
		$text = $this->parser->parse( $text );
		if ( 'yes' === $this->get_setting( 'pullquote_hanging_punctuation' ) ) {
			$text = $this->_apply_hanging_punctuation( $text );
		}

		// Set JSON for this element.
		$this->json = array(
			'role' => 'container',
			'layout' => array(
				'columnStart' => 3,
				'columnSpan' => 4
			),
			'components' => array(
				array(
					'role' => 'quote',
					'text' => $text,
					'format' => $this->parser->format,
					'layout' => 'pullquote-layout',
					'textStyle' => 'default-pullquote',
				)
			),
		);

		// Determine if there is a border specified.
		if ( 'none' === $this->get_setting( 'pullquote_border_style' ) ) {
			$values = $this->_set_pullquote_border( $values );
			$spec_name = 'pullquote-with-border-json';
		} else {
			$spec_name = 'pullquote-without-border-json';
		}

		// Register the JSON
		$this->register_json( $spec_name, $values );

		// Set component attributes.
		$this->_set_pullquote_anchor();
		$this->_set_pullquote_layout();
		$this->_set_pullquote_style();
	}

	/**
	 * Set the border for a blockquote.
	 *
	 * @param array $values
	 * @return array $values
	 * @access private
	 */
	private function _set_blockquote_border( $values ) {

		// Determine if there is a border specified.
		if ( 'none' === $this->get_setting( 'blockquote_border_style' ) ) {
			return $values;
		}

		// Set the border.
		$values['style']['border'] = array(
			'all' => array(
				'width' => $this->get_setting( 'blockquote_border_width' ),
				'style' => $this->get_setting( 'blockquote_border_style' ),
				'color' => $this->get_setting( 'blockquote_border_color' ),
			),
		);

		return $values;
	}

	/**
	 * Set the layout for a blockquote.
	 *
	 * @access private
	 */
	private function _set_blockquote_layout() {
		$this->register_layout(
			'blockquote-layout',
			'blockquote-layout'
		);
	}

	/**
	 * Set the style for a blockquote.
	 *
	 * @access private
	 */
	private function _set_blockquote_style() {
		$this->register_style(
			'default-blockquote',
			'default-blockquote',
			array(
				'fontName' => $this->get_setting( 'blockquote_font' ),
				'fontSize' => intval( $this->get_setting( 'blockquote_size' ) ),
				'textColor' => $this->get_setting( 'blockquote_color' ),
				'lineHeight' => intval( $this->get_setting( 'blockquote_line_height' ) ),
				'textAlignment' => $this->find_text_alignment(),
				'tracking' => intval( $this->get_setting( 'blockquote_tracking' ) ) / 100,
			),
			'textStyle'
		);
	}

	/**
	 * Sets the anchor settings for a pullquote.
	 *
	 * @access private
	 */
	private function _set_pullquote_anchor() {
		$this->set_anchor_position( Component::ANCHOR_AUTO );
	}

	/**
	 * Set the border for a pullquote.
	 *
	 * @param array $values
	 * @return array
	 * @access private
	 */
	private function _set_pullquote_border( $values ) {
		// Set the border.
		$values['style'] = array(
			'border' => array(
				'all' => array (
					'width' => $this->get_setting( 'pullquote_border_width' ),
					'style' => $this->get_setting( 'pullquote_border_style' ),
					'color' => $this->get_setting( 'pullquote_border_color' ),
				),
			),
		);
	}

	/**
	 * Set the layout for a pullquote.
	 *
	 * @access private
	 */
	private function _set_pullquote_layout() {
		$this->register_layout(
			'pullquote-layout',
			'pullquote-layout'
		);
	}

	/**
	 * Set the style for a pullquote.
	 *
	 * @access private
	 */
	private function _set_pullquote_style() {
		$this->register_style(
			'default-pullquote',
			'default-pullquote',
			array(
				'fontName' => $this->get_setting( 'pullquote_font' ),
				'fontSize' => intval( $this->get_setting( 'pullquote_size' ) ),
				'hangingPunctuation' => ( 'yes' === $this->get_setting( 'pullquote_hanging_punctuation' ) ),
				'textColor' => $this->get_setting( 'pullquote_color' ),
				'textTransform' => $this->get_setting( 'pullquote_transform' ),
				'lineHeight' => intval( $this->get_setting( 'pullquote_line_height' ) ),
				'textAlignment' => $this->find_text_alignment(),
				'tracking' => intval( $this->get_setting( 'pullquote_tracking' ) ) / 100,
			),
			'textStyle'
		);
	}
}
