#!/bin/bash


THEME=twentyfourteen
WP_DIR=/var/www/wordpress
TARGET_DIR=$WP_DIR/wp-content
WP_CLI=/usr/local/bin/wp
WP_SITE_URL=$($WP_CLI --path=$WP_DIR option get siteurl)


echo 'Cleaning plugins directory'
for i in `ls $TARGET_DIR/plugins`; do 
   $WP_CLI --path=$WP_DIR plugin deactivate `basename $i` --network
done
rm -rf $TARGET_DIR/plugins/*

echo 'Restoring plugins from source'
cp -r plugins/* $TARGET_DIR/plugins
for i in `ls $TARGET_DIR/plugins`; do 
   $WP_CLI --path=$WP_DIR plugin activate `basename $i` --network
done


echo 'Replacing theme: $THEME'
rm -r $TARGET_DIR/themes/$THEME
cp -r themes/* $TARGET_DIR/themes/
$WP_CLI --path=$WP_DIR theme activate $THEME


echo 'Importing content'
$WP_CLI --path=$WP_DIR site empty --yes
$WP_CLI --path=$WP_DIR import content/site-content.xml --authors=skip --skip=attachment --quiet
$WP_CLI --path=$WP_DIR --require=Front_Page_Command.php static-front-page 'Welcome'


echo 'Importing widgets'
$WP_CLI --path=$WP_DIR --require=Widgets_Command.php widgets import content/widgets.json --quiet


echo 'Configuring options'
$WP_CLI --path=$WP_DIR option update some_option '0'
$WP_CLI --path=$WP_DIR option update other_option 'the value'


