TwentyTen WordPress theme for MediaWiki
=======================================

This is a port of the TwentyTen WordPress theme to MediaWiki.

## Installation

1. Copy the files to the 'skins' directory of your MediaWiki install.

2. In `LocalSettings.php` set the following variables (they can be either
   relative or full URLs):

   ```
   $wgLogo             = "http://example.com/logo.jpg";
   $wgTwentytenHeader  = "$wgStylePath/twentyten/resources/twentyten/headers/fern.jpg";
   ```

   The header image should be 940 pixels wide.

3. Then edit Mediawiki:Sidebar and add the 'MENUBAR', 'PAGETOC', and 'LOGO' sections.
   A complete example sidebar with these added might look like the following:

   ```
   * MENUBAR
   ** mainpage|mainpage
   ** aboutpage|about
   ** Contact|Contact
   * LOGO
   * PAGETOC
   * navigation
   ** portal-url|portal
   ** helppage|help
   ** recentchanges-url|recentchanges
   ** randompage-url|randompage
   ```

   You can rearrange or delete any parts of Sidebar that you wish, but there
   does need to be some content or MediaWiki will give you default Navigation
   and Search sections.  Note also that if you remove the PAGETOC section, no ToC
   will be shown for any page.

   The toolbox section does not need to be kept, because it's moved to one of
   the columns in the footer.

4. Please report any problems or suggestions via Phabricator:
   https://phabricator.wikimedia.org/maniphest/task/edit/form/1/
   
## License

Version 2 of the GNU General Public License (as are MediaWiki and WordPress).

## Development

All the files in `resources/twentyten/` are direct unmodified copies from the WordPress theme.
