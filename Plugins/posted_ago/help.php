<?php ?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 0px 20px 20px;">
        <div>
           <fieldset>
                <legend>
                    <h1><?php _e('Plugin Configuration', 'Posted Ago'); ?></h1>
                </legend>
                <h2>
                    <?php _e('What is Posted Ago Plugin?', 'Posted Ago'); ?>
                </h2>
                <p>
                    <?php _e('Posted Ago allows the Published and Modified Date to show in Real time. example <b>2 Hours 3 Seconds Ago</b>', 'Posted Ago'); ?>
                </p>                           
                <h2>
                    <?php _e('How to Configure the Posted Ago Plugin', 'Posted Ago'); ?>
                </h2>
                <p>
				    <?php _e('Locate this in your themes item.php, main.php, search_list.php', 'Posted Ago'); ?>.
                </p>
                <pre>
                osc_format_date
                </pre>
                <pre>
                osc_format_date(osc_item_pub_date()) ;
                </pre>
                <pre>
                osc_format_date( osc_item_mod_date(), "modified") ;
                </pre>
                <pre>
                osc_format_date(osc_premium_pub_date()) ;
                </pre>
                <p>
                     <?php _e('Simple replace <b>osc_format_date</b> with <b>posted_ago</b>', 'Posted Ago'); ?>
                </p>
                <pre>                    
		posted_ago
                </pre>
                <pre>
                posted_ago(osc_item_pub_date()) ;
                </pre>
                <pre>
                posted_ago( osc_item_mod_date()) ;
                </pre>
                <pre>
                posted_ago(osc_premium_pub_date()) ;
                </pre>
                <p>
                    <?php _e('You should now have the Posted Ago Plugin working.', 'Posted Ago'); ?>
                </p><br /> <br />
                <h2>
                    <?php _e('How to Uninstall the Posted Ago Plugin', 'Posted Ago'); ?>
                </h2>
                <p>
                    <?php _e('To uninstall this plugin you will need to edit the item.php, main.php, search_list.php and find this below', 'Posted Ago'); ?>
                </p>
                <pre>                    
		posted_ago
                </pre>
                <p>
                    <?php _e('Simply Replace it with this', 'Posted Ago'); ?>
                </p>
                <pre>
		osc_format_date
                </pre> 
                <p>
                    <?php _e('Then Click Manage Plugins - Posted Ago - Uninstall to remove the plugin', 'Posted Ago'); ?>
                </p>     
            </fieldset>
        </div>
    </div>
</div>