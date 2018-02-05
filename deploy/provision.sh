if [ -z "$1" ]; then
	echo "##################################################################"
	echo "### ERROR                                                      ###"
	echo "### Usage: provision.sh [environment]                          ###"
	echo "### Please provide an environment argument to run this script. ###"
	echo "##################################################################"
	exit
fi
ENV=$1

### set install path
SITENAME=sandbox-api
INSTALLDIR=/websites/${SITENAME}

CRON_FILE=${INSTALLDIR}/deploy/cron/crontab.${ENV}
CRON_START='# CRON START'
CRON_END='# CRON END'

echo " "
echo "#######################"
echo "### File management ###"
echo "#######################"

# cache/ is being ignored for rsync, so these folders need to be created
echo "Provision Twig cache."
mkdir -p ${INSTALLDIR}/cache/Twig/cache

if [ -d ${INSTALLDIR}/cache ]; then
	chmod -R 777 ${INSTALLDIR}/cache
fi

# remove any files in the Twig cache during provisioning:
rm -rf ${INSTALLDIR}/cache/Twig/cache/*

# logs/ needs to be created and chmoded
echo "Provision logs directory."
mkdir -p ${INSTALLDIR}/logs

if [ -d ${INSTALLDIR}/logs ]; then
	chmod -R 777 ${INSTALLDIR}/logs
fi

# www/static/uploads/ needs to be created and chmoded
echo "Provision uploads directory."
mkdir -p ${INSTALLDIR}/www/static/uploads/

if [ -d ${INSTALLDIR}/www/static/uploads/ ]; then
	chmod -R 777 ${INSTALLDIR}/www/static/uploads/
fi

echo " "
echo "####################"
echo "### Config setup ###"
echo "####################"

echo "Moving www/config.$ENV.ini to www/config.ini"

CONFIG_FILE=${INSTALLDIR}/deploy/config/config.${ENV}.ini

if [ -f ${CONFIG_FILE} ]; then
	cp ${CONFIG_FILE} ${INSTALLDIR}/config.ini
fi

echo " "
echo "##################"
echo "### Cron setup ###"
echo "##################"

echo "Adding a crontab of type $ENV"

if [ ! -f ${CRON_FILE} ]; then
	echo "$CRON_FILE not found"
else
	(crontab -l | sed "/^$CRON_START/,/^$CRON_END/d"; cat ${CRON_FILE}) | crontab -
fi

echo " "
echo "###############################"
echo "### Updating and installing ###"
echo "###############################"

apt-get update

### install the basics
export DEBIAN_FRONTEND=noninteractive
apt-get install -y php5 php5-cli apache2 git curl mysql-client php5-mysql vim libapache2-mod-php5 php5-curl

### composer

echo " "
echo "########################"
echo "### Composer install ###"
echo "########################"

if [ -f /usr/local/bin/composer ]; then
	echo "Composer already installed. Checking for updates..."
	composer self-update
else
	echo "Installing composer..."
	curl -sS https://getcomposer.org/installer | php
	mv composer.phar /usr/local/bin/composer
fi

cd ${INSTALLDIR}
composer install

### mysql db setup

echo " "
echo "########################"
echo "### Database install ###"
echo "########################"

if [ -f ${INSTALLDIR}/deploy/db/init.sql ]; then
	if [ ${ENV} == 'local' ]; then
		apt-get install -y mysql-server
		mysql < ${INSTALLDIR}/deploy/db/init.sql
	fi
fi

MIGRANTENV='local'
if [ ${ENV} == 'prod' ];then
	MIGRANTENV='production'
elif [ ${ENV} == 'dev' ]; then
	MIGRANTENV='development'
fi

echo "Running migrations for the migrant '${MIGRANTENV}' environment"

# migrant install
cd ${INSTALLDIR}/deploy/db
${INSTALLDIR}/vendor/bin/migrant up ${MIGRANTENV}

### apache stuff

echo " "
echo "######################"
echo "### Apache install ###"
echo "######################"

# display all PHP errors
sed -i "s/display_errors = Off/display_errors = On/g" /etc/php5/apache2/php.ini

cp ${INSTALLDIR}/deploy/httpd.conf /etc/apache2/sites-available/${SITENAME}.conf
sed -i "s/sitename/${SITENAME}/g" /etc/apache2/sites-available/${SITENAME}.conf
a2dissite 000-default.conf
a2ensite ${SITENAME}
a2enmod rewrite
a2enmod expires
service apache2 restart
