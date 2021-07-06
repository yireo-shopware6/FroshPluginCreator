# Frosh Plugin Creator
This plugin enhances the Shopware 6 command `plugin:create`

## Installation

    composer require frosh/plugin-creator
    bin/console plugin:refresh
    bin/console plugin:install --activate FroshPluginCreator

## Default behaviour
Create a plugin:

    bin/console plugin:create SwagExample

## Modified behaviour

    bin/console plugin:create SwagExample --namespace 'Swag\Example' --composer-name 'swag/shopware6-example' --description 'My Swag Example'