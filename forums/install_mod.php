<?php
/***********************************************************************/

// Some info about your mod.
$mod_title      = 'New Private Messaging System';
$mod_version    = '1.9.0';
$release_date   = '2021-01-27';
$author         = 'Visman';
$author_email   = 'mio.visman@yandex.ru';

// Versions of FluxBB this mod was created for. A warning will be displayed, if versions do not match
$fluxbb_versions= array('1.5.11');

// Set this to false if you haven't implemented the restore function (see below)
$mod_restore	= true;

function update()
{
	global $db;

	$db->drop_field('pms_new_block', 'bl_user') or error('Unable to drop bl_user field', __FILE__, __LINE__, $db->error());
	$db->drop_field('pms_new_posts', 'post_seen') or error('Unable to drop post_seen field', __FILE__, __LINE__, $db->error());
}

// This following function will be called when the user presses the "Install" button
function install()
{
	global $db, $db_type, $pun_config;


	$schema = array(
		'FIELDS'		=> array(
			'bl_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'bl_user_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'INDEXES'		=> array(
			'bl_id_idx'	=> array('bl_id'),
			'bl_user_id_idx'	=> array('bl_user_id')
		)
	);

	$db->create_table('pms_new_block', $schema) or error('Unable to create pms_new_block table', __FILE__, __LINE__, $db->error());

	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'poster'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'poster_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'poster_ip'		=> array(
				'datatype'		=> 'VARCHAR(39)',
				'allow_null'	=> true
			),
			'message'		=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'hide_smilies'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'posted'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'edited'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'edited_by'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> true
			),
			'post_new'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'topic_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'topic_id_idx'	=> array('topic_id'),
			'multi_idx'		=> array('poster_id', 'topic_id')
		)
	);

	$db->create_table('pms_new_posts', $schema) or error('Unable to create pms_new_posts table', __FILE__, __LINE__, $db->error());

	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'topic'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'starter'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'starter_id'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'to_user'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'to_id'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'replies'	=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_posted'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_poster'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'see_st'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'see_to'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'topic_st'		=> array(
				'datatype'		=> 'TINYINT(4)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'topic_to'		=> array(
				'datatype'		=> 'TINYINT(4)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'multi_idx_st'		=> array('starter_id', 'topic_st'),
			'multi_idx_to'		=> array('to_id', 'topic_to')
		)
	);

	$db->create_table('pms_new_topics', $schema) or error('Unable to create pms_new_topics table', __FILE__, __LINE__, $db->error());

	$db->add_field('groups', 'g_pm', 'TINYINT(1)', false, 1) or error('Unable to add g_pm field', __FILE__, __LINE__, $db->error());
	$db->add_field('groups', 'g_pm_limit', 'INT(10) UNSIGNED', false, 100) or error('Unable to add g_pm_limit field', __FILE__, __LINE__, $db->error());

	$db->add_field('users', 'messages_enable', 'TINYINT(1)', false, 1) or error('Unable to add messages_enable field', __FILE__, __LINE__, $db->error());
	$db->add_field('users', 'messages_email', 'TINYINT(1)', false, 0) or error('Unable to add messages_email field', __FILE__, __LINE__, $db->error());
	$db->add_field('users', 'messages_flag', 'TINYINT(1)', false, 0) or error('Unable to add messages_flag field', __FILE__, __LINE__, $db->error());
	$db->add_field('users', 'messages_new', 'INT(10) UNSIGNED', false, 0) or error('Unable to add messages_new field', __FILE__, __LINE__, $db->error());
	$db->add_field('users', 'messages_all', 'INT(10) UNSIGNED', false, 0) or error('Unable to add messages_all field', __FILE__, __LINE__, $db->error());
	$db->add_field('users', 'pmsn_last_post', 'INT(10) UNSIGNED', true) or error('Unable to add pmsn_last_post field', __FILE__, __LINE__, $db->error());

	$db->query('UPDATE '.$db->prefix.'groups SET g_pm_limit=0 WHERE g_id='.PUN_ADMIN) or error('Unable to merge groups', __FILE__, __LINE__, $db->error());

	// Insert config data
	$config = array(
		'o_pms_enabled'		=> '1',
		'o_pms_min_kolvo'	=> '0',
		'o_pms_flasher'		=> '0',
		'o_crypto_pas'		=> random_pass(25),
	);

	foreach ($config as $conf_name => $conf_value)
	{
		if (!array_key_exists($conf_name, $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES(\''.$conf_name.'\', \''.$db->escape($conf_value).'\')')
				or error('Unable to insert into table '.$db->prefix.'config. Please check your configuration and try again.');
	}

	// Delete all .php files in the cache (someone might have visited the forums while we were updating and thus, generated incorrect cache files)
	forum_clear_cache();
}

// This following function will be called when the user presses the "Restore" button (only if $mod_restore is true (see above))
function restore()
{
	global $db, $db_type, $pun_config;

	$db->drop_table('pms_new_block') or error('Unable to drop table pms_new_block', __FILE__, __LINE__, $db->error());
	$db->drop_table('pms_new_posts') or error('Unable to drop table pms_new_posts', __FILE__, __LINE__, $db->error());
	$db->drop_table('pms_new_topics') or error('Unable to drop table pms_new_topics', __FILE__, __LINE__, $db->error());
	$db->drop_field('groups', 'g_pm') or error('Unable to drop g_pm field', __FILE__, __LINE__, $db->error());
	$db->drop_field('groups', 'g_pm_limit') or error('Unable to drop g_pm_limit field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'messages_enable') or error('Unable to drop messages_enable field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'messages_email') or error('Unable to drop messages_email field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'messages_flag') or error('Unable to drop messages_flag field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'messages_new') or error('Unable to drop messages_new field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'messages_all') or error('Unable to drop messages_all field', __FILE__, __LINE__, $db->error());
	$db->drop_field('users', 'pmsn_last_post') or error('Unable to drop pmsn_last_post field', __FILE__, __LINE__, $db->error());

	$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name LIKE \'o_pms_%\'') or error('Unable to remove config entries', __FILE__, __LINE__, $db->error());;
	$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name LIKE \'o_crypto_pas\'') or error('Unable to remove config entries', __FILE__, __LINE__, $db->error());;

	forum_clear_cache();
}

/***********************************************************************/

// DO NOT EDIT ANYTHING BELOW THIS LINE!


// Circumvent maintenance mode
define('PUN_TURN_OFF_MAINT', 1);
define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';

// only admin
if ($pun_user['g_id'] != PUN_ADMIN)
	message('****** For administrator ONLY!!! ******');

// We want the complete error message if the script fails
if (!defined('PUN_DEBUG'))
	define('PUN_DEBUG', 1);

// Make sure we are running a FluxBB version that this mod works with
$version_warning = !in_array($pun_config['o_cur_version'], $fluxbb_versions);

$style = (isset($pun_user)) ? $pun_user['style'] : $pun_config['o_default_style'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo pun_htmlspecialchars($mod_title) ?> installation</title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $style.'.css' ?>" />
</head>
<body>

<div id="punwrap">
<div id="puninstall" class="pun" style="margin: 10% 20% auto 20%">

<?php

if (isset($_POST['form_sent']))
{
	if (isset($_POST['update']))
	{
		// Run the update function (defined above)
		update();

?>
<div class="block">
	<h2><span>Updating successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully prepared for <?php echo pun_htmlspecialchars($mod_title) ?>. See update_172_to_180.txt for further instructions.</p>
		</div>
	</div>
</div>
<?php

	}
	else if (isset($_POST['install']))
	{
		// Run the install function (defined above)
		install();

?>
<div class="block">
	<h2><span>Installation successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully prepared for <?php echo pun_htmlspecialchars($mod_title) ?>. See readme.txt for further instructions.</p>
		</div>
	</div>
</div>
<?php

	}
	else
	{
		// Run the restore function (defined above)
		restore();

?>
<div class="block">
	<h2><span>Restore successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully restored.</p>
		</div>
	</div>
</div>
<?php

	}
}
else
{

?>
<div class="blockform">
	<h2><span>Mod installation</span></h2>
	<div class="box">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?foo=bar">
			<div><input type="hidden" name="form_sent" value="1" /></div>
			<div class="inform">
				<p>This script will update your database to work with the following modification:</p>
				<p><strong>Mod title:</strong> <?php echo pun_htmlspecialchars($mod_title.' '.$mod_version) ?></p>
				<p><strong>Author:</strong> <?php echo pun_htmlspecialchars($author) ?> (<a href="mailto:<?php echo pun_htmlspecialchars($author_email) ?>"><?php echo pun_htmlspecialchars($author_email) ?></a>)</p>
				<p><strong>Disclaimer:</strong> Mods are not officially supported by FluxBB. Mods generally can't be uninstalled without running SQL queries manually against the database. Make backups of all data you deem necessary before installing.</p>
<?php if ($mod_restore): ?>
				<p>If you've previously installed this mod and would like to uninstall it, you can click the Restore button below to restore the database.</p>
<?php endif; ?>
<?php if ($version_warning): ?>
				<p style="color: #a00"><strong>Warning:</strong> The mod you are about to install was not made specifically to support your current version of FluxBB (<?php echo $pun_config['o_cur_version']; ?>). This mod supports FluxBB versions: <?php echo pun_htmlspecialchars(implode(', ', $fluxbb_versions)); ?>. If you are uncertain about installing the mod due to this potential version conflict, contact the mod author.</p>
<?php endif; ?>
			</div>
			<p class="buttons"><input type="submit" name="update" value="Update DB to v1.8.0" /><input type="submit" name="install" value="Install" /><?php if ($mod_restore): ?><input type="submit" name="restore" value="Restore" /><?php endif; ?></p>
		</form>
	</div>
</div>
<?php

}

?>

</div>
</div>

</body>
</html>
