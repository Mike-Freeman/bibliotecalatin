<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require('./components/com_booklibrary/admin.booklibrary.class.conf.php');


// load language 
global $mosConfig_absolute_path, $mosConfig_lang,$mosConfig_live_site;

if (file_exists($mosConfig_absolute_path."/components/com_booklibrary/language/{$mosConfig_lang}.php" )) {
	include_once($mosConfig_absolute_path."/components/com_booklibrary/language/{$mosConfig_lang}.php" );
} else {
	include_once($mosConfig_absolute_path."/components/com_booklibrary/language/english.php" );
       }

?>

<table class="adminform bl_admin_about_tab_releasenote my_table">
	<tr>
		<td colspan="2">
			<h3><?php echo _BOOKLIBRARY_DOC_GENERAL_INFO;?></h3>
		</td>
	</tr>
	<tr>
		<td>
			<strong> <?php echo _BOOKLIBRARY_DOC_VERSION;?></strong>
		</td>
		<td>
			<strong>v <?php echo $booklibrary_configuration['release']['version'];?></strong>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php echo _BOOKLIBRARY_DOC_RELEASE_DATE;?></strong>
		</td>
		<td>
			<strong><?php echo $booklibrary_configuration['release']['date'];?></strong>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php echo _BOOKLIBRARY_DOC_PROJECTLINK;?></strong>
		</td>
		<td>
			<strong>
				<a href="http://www.ordasoft.com" target="blank">www.ordasoft.com</a>
			</strong>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php echo _BOOKLIBRARY_DOC_PROJECT_HOST;?></strong>
		</td>
		<td>
			<strong>Andrey Kvasnevskiy (<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)
			</strong>
		</td>
	</tr>
  <tr>
    <td valign="top">
      <strong><?php echo _BOOKLIBRARY_DOC_LICENSE;?></strong>
    </td>
    <td>
      <strong>
        <a href="<?php echo $mosConfig_live_site."/administrator/components/com_booklibrary/doc/LICENSE.txt"; ?>" 
           target="blank">License</a>
      </strong>
      <br />
       <?php echo _BOOKLIBRARY_DOC_WARRANTY;?>
    </td>
  </tr>
  <tr>
    <td valign="top">
      <strong>README:</strong>
    </td>
    <td>
      <strong>
        <a href="<?php echo $mosConfig_live_site."/administrator/components/com_booklibrary/doc/README.txt"; ?>" 
           target="blank">README</a>
      </strong>
    </td>
  </tr>
	<tr >
		<td valign="top">
			<strong><?php echo _BOOKLIBRARY_DOC_DEVELOP;?></strong>
		</td>
		<td>
        <li><b>v 3.0 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 2.2 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 2.1 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 2.0 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 1.5.3.1 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 1.5.3 Free</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>);Aleksey Pakholkov</li>
			<ul>
        <li><b>v 1.5.2.4 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>);Aleksey Pakholkov</li>
				<li><b>v 1.5.2.3 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 1.5.2.2 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
        <li><b>v 1.5.2.1 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
				<li><b>v 1.5.2 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
				<li><b>v 1.0.2 Shop</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>)</li>
				<li><b>v 1.0.1</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>), Nikolay Salionovych(<a href="mailto:salionovych@ukr.net" >salionovych@ukr.net</a>)</li>
				<li><b>v 1.0</b> - Orda Soft: Andrey Kvasnevskiy(<a href="mailto:akbet@ordasoft.com" >akbet@ordasoft.com</a>), Nikolay Salionovych(<a href="mailto:salionovych@ukr.net" >salionovych@ukr.net</a>)</li>
				<li><b>v 0.7</b> - Andrzej Waland (<a href="www.waland.pl/mambo" target="blank">www.waland.pl/mambo</a>), Leon Treff (<a href="mailto:treff@pt.lu" >treff@pt.lu</a>) </li>
				<li><b>v 0.6</b> - Gerd Saurer <a href="http://www.sapoba.com/~gerd/" target="blank"><?php echo _BOOKLIBRARY_DOC_HOMEPAGE;?></a> (<a href="mailto:goertsch@user.mamboforge.net" >goertsch@user.mamboforge.net</a>) </li>
			</ul>
		</td>
	</tr>
</table>

