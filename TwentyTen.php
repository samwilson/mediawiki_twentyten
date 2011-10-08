<?php
/**
 * Wordpress TwentyTen theme adapted for MediaWiki.
 *
 * @link http://github.com/samwilson/mediawiki_twentyten
 * @ingroup Skins
 */
if( !defined( 'MEDIAWIKI' ) ) die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinTwentyTen extends SkinTemplate {

	var $skinname = 'twentyten', $stylename = 'twentyten',
		$template = 'TwentyTenTemplate', $useHeadElement = true;

	/** @var string The cache key under which to store the current page's TOC. */
	var $savedTocCacheKey;

	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;

		parent::setupSkinUserCss( $out );

		$out->addStyle( 'twentyten/twentyten.css', 'screen' );
		$out->addStyle( 'twentyten/rtl.css',       'screen', '', 'rtl' );
		$out->addStyle( 'twentyten/main.css',      'screen' );

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

}

/**
 * @todo document
 * @ingroup Skins
 */
class TwentyTenTemplate extends QuickTemplate {
	var $skin;
	/**
	 * Template filter callback for TwentyTen skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgRequest, $wgSitename, $wgTwentytenHeader, $wgStylePath;

		$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );
		
		$this->set( 'sitename', $wgSitename );
		
		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

		$this->html( 'headelement' );
?><div id="wrapper" class="hfeed">
	<div id="header">
		<div id="masthead">
			<div id="branding" role="banner">
				<h1 id="site-title">
					<a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>" rel="home">
						<?php echo $this->text('sitename') ?>
					</a>
				</h1>
				<div id="site-description"><?php $this->msg('tagline') ?></div>
				<?php $headerSrc = (isset($wgTwentytenHeader)) ? $wgTwentytenHeader : "$wgStylePath/twentyten/headers/path.jpg" ?>
				<img src="<?php echo $headerSrc ?>" alt="Header image" />
			</div><!-- #branding -->

			<div id="access" role="navigation">
				<div class="menu">
					<ul>
						<?php foreach ($this->data['sidebar']['MENUBAR'] as $menuitem): ?>
						<li class="page_item">
							<a href="<?php echo $menuitem['href'] ?>"><?php echo $menuitem['text'] ?></a>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div><!-- #access -->
		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main" <?php $this->html("specialpageattributes") ?>>
		<div id="container">
			<div id="content" role="main">			
				<div>
					<h2 class="entry-title"><?php $this->html('title') ?></h2>
					<div class="entry-meta">
						<?php $this->html('subtitle') ?>
					</div><!-- .entry-meta -->
					
					<div class="entry-content">
						<?php $this->html('bodytext') ?>
						<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
						<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
					</div>
				</div>
			
			</div><!-- #content -->
		</div><!-- #container -->
		
		<div id="primary" class="widget-area" role="complementary">
			<ul class="xoxo">
						
			<?php $sidebar = $this->data['sidebar'];
			foreach ($sidebar as $boxName => $cont) {
				if ( $boxName == 'SEARCH' ) {
					$this->searchBox();
				} elseif ( $boxName == 'LOGO' ) {
					$this->logo();
				} elseif ( $boxName == 'LANGUAGES' ) {
					$this->languageBox();
				} elseif ( $boxName == 'PAGETOC' ) {
					$this->pageTocBox();
				} elseif ($boxName!='MENUBAR' && $boxName!='TOOLBOX') {
					$this->customBox( $boxName, $cont );
				}
			} ?>
			
			</ul>
		</div><!-- #primary .widget-area -->

	</div><!-- #main -->

	<div id="footer" role="contentinfo" <?php $this->html('userlangattributes') ?>>
		<div id="colophon">
		
			<div id="footer-widget-area" role="complementary">
			
				<div id="first" class="widget-area">
					<ul class="xoxo">
			<?php
				foreach($this->data['content_actions'] as $key => $tab) {
					echo '
				 <li id="' . Sanitizer::escapeId( "ca-$key" ) . '"';
					if( $tab['class'] ) {
						echo ' class="'.htmlspecialchars($tab['class']).'"';
					}
					echo '><a href="'.htmlspecialchars($tab['href']).'"';
					# We don't want to give the watch tab an accesskey if the
					# page is being edited, because that conflicts with the
					# accesskey on the watch checkbox.  We also don't want to
					# give the edit tab an accesskey, because that's fairly su-
					# perfluous and conflicts with an accesskey (Ctrl-E) often
					# used for editing in Safari.
				 	if( in_array( $action, array( 'edit', 'submit' ) )
				 	&& in_array( $key, array( 'edit', 'watch', 'unwatch' ))) {
				 		echo $skin->tooltip( "ca-$key" );
				 	} else {
				 		echo $skin->tooltipAndAccesskey( "ca-$key" );
				 	}
				 	echo '>'.htmlspecialchars($tab['text']).'</a></li>';
				} ?>
					</ul>
				</div><!-- #first .widget-area -->
			
				<div id="second" class="widget-area">
					<ul class="xoxo">
					<?php $this->toolbox() ?>
					</ul>
				</div><!-- #second .widget-area -->
				
				<div id="third" class="widget-area">
					<ul class="xoxo" <?php $this->html('userlangattributes') ?>>
					<?php foreach($this->data['personal_urls'] as $key => $item) { ?>
						<li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>"<?php
						if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
						echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskey('pt-'.$key) ?><?php
						if(!empty($item['class'])) { ?> class="<?php
						echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
						echo htmlspecialchars($item['text']) ?></a></li>
					<?php } ?>
					</ul>
				</div><!-- #third .widget-area -->

				<div id="fourth" class="widget-area">
					<ul class="xoxo">
					<?php if($this->data['copyrightico']) echo '<li>'.$this->html('copyrightico').'</li>';
					// Generate additional footer links
					$footerlinks = array('lastmod', 'viewcount', 'numberofwatchingusers', 'credits', 'copyright', 'privacy', 'about', 'disclaimer', 'tagline');
					$validFooterLinks = array();
					foreach( $footerlinks as $aLink ) {
						if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) $validFooterLinks[] = $aLink;
					}
					foreach( $validFooterLinks as $aLink ):
						if( isset( $this->data[$aLink] ) && $this->data[$aLink] ): ?>
					<li id="<?php echo $aLink ?>"><?php $this->html($aLink) ?></li>
						<?php endif ?>
					<?php endforeach ?>
					</ul>
				</div><!-- #fourth .widget-area -->				
				
			</div><!-- #footer-widget-area -->
		
			<div id="site-info">
				<a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>" rel="home"><?php echo $this->text('sitename') ?></a>
			</div><!-- #site-info -->
			<div id="site-generator">Proudly powered by
				<a href="http://mediawiki.org/" rel="generator">MediaWiki</a>
				and the <a href="http://wordpress.org/extend/themes/twentyten" rel="generator">TwentyTen WordPress theme</a>.
			</div>
		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
</body>
</html>



	<?php
	wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/
	function searchBox() {
		global $wgUseTwoButtonsSearchForm;
?>
	<li class="widget-container">
		<h3 class="widget-title"><label for="searchInput"><?php $this->msg('search') ?></label></h3>
			<form action="<?php $this->text('wgScript') ?>" id="searchform">
				<input type='hidden' name="title" value="<?php $this->text('searchtitle') ?>"/>
				<?php
		echo Html::input( 'search',
			isset( $this->data['search'] ) ? $this->data['search'] : '', 'search',
			array(
				'id' => 'searchInput',
				'title' => $this->skin->titleAttrib( 'search' ),
				'accesskey' => $this->skin->accesskey( 'search' )
			) ); ?>

				<input type='submit' name="go" class="searchButton" id="searchGoButton"
				value="<?php $this->msg('searcharticle') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-go' ); ?> />

			</form>
	</li>
<?php
	}

	function logo() {
		?>
		<li class="widget-container logo">
			<a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"<?php
			echo $this->skin->tooltipAndAccesskey('p-logo') ?>>
				<img src="<?php $this->text('logopath') ?>" alt="Site Logo" />
			</a>
		</li>
		<?php
	}

	/*************************************************************************************************/
	function toolbox() {
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked-toolbox') ?></a></li>
<?php 		}
		}
		if( isset( $this->data['nav_urls']['trackbacklink'] ) && $this->data['nav_urls']['trackbacklink'] ) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><a id="<?php echo Sanitizer::escapeId( "feed-$key" ) ?>" href="<?php
					echo htmlspecialchars($feed['href']) ?>" rel="alternate" type="application/<?php echo $key ?>+xml" class="feedlink"<?php echo $this->skin->tooltipAndAccesskey('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>" rel="alternate"<?php echo $this->skin->tooltipAndAccesskey('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $this->skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'TwentyTenTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) );
	}

	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) {
?>
	<li class="widget-container">
		<h3 class="widget-title" <?php $this->html('userlangattributes') ?>><?php $this->msg('otherlanguages') ?></h3>
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
	</li>
<?php
		}
	}

	/*************************************************************************************************/
	function pageTocBox() {
		global $parserMemc;
		echo '<li class="widget-container">';
		echo $parserMemc->get($this->skin->savedTocCacheKey);
		echo '</li>';
	}

	/*************************************************************************************************/
	function customBox( $bar, $cont ) { ?>

		<li class='widget-container' id='<?php echo Sanitizer::escapeId( "p-$bar" ) ?>'<?php echo $this->skin->tooltip('p-'.$bar) ?>>
		<h3 class="widget-title">
			<?php $out = wfMsg( $bar );
			if (wfEmptyMsg($bar, $out)) echo htmlspecialchars($bar);
			else echo htmlspecialchars($out); ?>
		</h3>
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey($val['id']) ?>>
					<?php echo htmlspecialchars($val['text']) ?></a>
				</li>
<?php			} ?>
			</ul>
<?php   } else {
			# allow raw HTML block to be defined by extensions
			print $cont;
		}
		echo '</li>';

	} // customBox()
	
} // end of class


