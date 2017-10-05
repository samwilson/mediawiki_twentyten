<?php
/**
 * Wordpress TwentyTen theme adapted for MediaWiki.
 *
 * @link https://github.com/samwilson/mediawiki_twentyten
 * @ingroup Skins
 */
if( !defined( 'MEDIAWIKI' ) ) die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @ingroup Skins
 */
class SkinTwentyTen extends SkinTemplate {

	public $skinname = 'twentyten';

	public $stylename = 'twentyten';

	public $template = 'TwentyTenTemplate';

	public $useHeadElement = true;

	/** @var string The cache key under which to store the current page's TOC. */
	public $savedTocCacheKey;

	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModules( [ 'skins.TwentyTen' ] );
	}

	/**
	 * Set the title, and create the cache key under which to store this page's TOC.
	 *
	 * @param Title $t The title to use.
	 *
	 * @see Skin::setTitle()
	 * @return void
	 */
	function setTitle( $t ) {
		parent::setTitle($t);
		$article_id = $this->mTitle->getArticleID();
		$this->savedTocCacheKey = wfMemcKey('twentyten', 'saved-toc', $article_id);
	}

	/**
	 * Completes the HTML for this page's TOC (note the closing UL tag) in a form
	 * suitable for use in the sidebar, and saves it to the cache for later use in
	 * {@link TwentyTenTemplate::pageTocBox()}.
	 *
	 * @param string $toc HTML of the Table Of Contents for this page.
	 *
	 * @see Linker::tocList()
	 * @see TwentyTenTemplate::pageTocBox()
	 * @return string An empty string; the TOC is pulled from cache, later.
	 */
	function tocList($toc) {
		global $parserMemc;
        $title = wfMsgHtml('toc');
		$savedToc = '<h3 class="widget-title">'.$title."</h2>".$toc."</ul>";
		$parserMemc->set($this->savedTocCacheKey, $savedToc);
		return '';
	}

	public function tooltipAndAccesskey( $name ) {
        return join(' ', Linker::tooltipAndAccesskeyAttribs( $name ) );
	}

	public function tooltip( $name ) {
        return $name;
	}

	public function titleAttrib() {
        
	}

	public function accesskey(  ) {
        
	}
}

