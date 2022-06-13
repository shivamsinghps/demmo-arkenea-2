php bin/console cache:clear
pear channel-discover pear.phing.info
pear install --alldeps phing/phing
phing configure
phing build
phing deploy
php phing.phar deploy -verbose