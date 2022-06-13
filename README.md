# Fund My Textbook

## Application setup and configuration

Project root directory must contain the following files:

- build.xml -- phing tasks configuration file
- build.properties.dist -- example of local project configuration
- composer.lock -- Composer lock-file with that contains strict versions of installed bundles 
- composer.json -- project dependencies configuration, and build tasks list
- build/syberry-phpmd-1.0.xml -- configuration for phpmd analysis

A sample properties file can be found in `build.properties.dist`. Use following sequence to build project (all 3rd party dependencies should be satisfied):

1. `cp build.properties.dist build.properties`
2. Edit `build.properties` file, set parameters according to your environment 
3. `cp environment/.env.dist environment/.envs`
4. Edit `environment/.env` file, set parameters according to your environment 
5. `cd environment`
5. `docker-compose up`

We use bower as a front-end package manager

## Build targets

There are few main build target of application that could be executed using `phing`:

1. `configure`
    
    This target creates all required configuration files of the application. It maps properties from the environment or property-file into corresponding configs.

2. `build`

    This target installs all dependencies of the project. That include installation of database (database migrations), 3rd party PHP bundles, and JavaScript libraries.

3. `test`

    This target launches different kind of tests to verify integrity of the project. That step could be launched only when all project dependencies are satisfied.

4. `deploy`

    THis target complets installation of the application. That includes reset of the cache, update of JavaScrpts, installation of crontab commands.

## Project build variables

Use following list of variables to set up project:

- **domain** - domain name of application that will be used to build absolute links on pages.
- **database_host** - host name or IP address of database host.
- **database_post** - port number of database server.
- **database_name** - name of database scheme.
- **database_user** - name of user that permitted to access application database (with read-write permissions).
- **database_password** - access password of database user.
- **mailer_transport** - type of the transport that will be used for e-mail delivery ([refer to the documentation to find out possible values](https://symfony.com/doc/3.4/email.html#configuration)).
- **mailer_host** - host name or IP address of e-mail server.
- **mailer_port** - port number of e-mail server.
- **mailer_user** - user name to authenticate on e-mail server.
- **mailer_password** - access password of the user.
- **sender_email** - e-mail address that will be displayed in "From:" field of e-mail.
- **sender_name** - display name of e-mail sender.
- **nebook_endpoint** - URL of Nebook API (JSON implementation)
- **nebook_wsdl** - WSDL of Nebook API (SOAP implementation)
- **nebook_xmlns** - XMLNS of Nebook API (SOAP implementation)
- **nebook_bookstore_id** - unique identifier of bookstore that will be used to interact with Nebook API.
- **nebook_user** - Nebook API user to call Nebook methods.
- **nebook_password** - access password of Nebook API user.
- **nebook_cache_lifetime** - number of seconds how much cache from Nebook will be stored.
- **stripe_secret_key** - secret key for integration with Stripe.
- **stripe_public_key** - key for integration with Stripe.
- **stripe_live_mode** - test or live mode of Stripe API.
- **csrf_secret** - unique secret key for the symfony application.
- **node_bin** - full path to nodeJS executable.
- **node_path** - full path to nodeJS modules.
- **npm_bin** - full path to nmp executable.
- **cache_server** - DSN of cache server or NULL.
- **avatar_storage** - string descriptor of avatar storage location (could be `s3://id:key@region/bucket` with optional `id` and `key` or `file:///web/root/related/location`)
- **s3_key** - key credential for integration with S3.
- **s3_secret** - secret key credential for integration with S3.
- **aws_region** - AWS server location region
- **aws_key** - key credential for integration with AWS.
- **aws_secret** - secret key credential for integration with AWS.
- **social_facebook_link** - Link of Facebook 
- **social_twitter_link** - Link of Twitter 
- **social_linkedin_link** - Link of Linkedin 
- **social_google_plus_link** - Link of Google+ 
- **social_pinterest_link** - Link of Pinterest 
### Dwolla
- **dwolla_endpoint** = URL of Dwolla API
- **dwolla_client_id** = Aka App Key of Dwolla API
- **dwolla_client_key** = Aka Secret of Dwolla API
- **dwolla_webhooks_token** = Secret of your webhook handlers. When Dwolla will send webhooks, it encrypt request body use this secret.
- **dwolla_webhooks_endpoint** = Endpoint of your webhook handler. Dwolla will send webhooks on this URL
- **dwolla_fmt_funding_source** = Account funding source IRI
- **dwolla_bookstore_email** = Email used to send deposit notification
- **dwolla_bookstore_customer_first_name** = Bookstore customer first name. Used for create Dwolla receive only user.
- **dwolla_bookstore_customer_last_name** = Bookstore customer last name. Used for create Dwolla receive only user.
- **dwolla_bookstore_customer_email** =  A Bookstore customer email. Used for create Dwolla receive only user.
- **dwolla_bookstore_customer_business_name** = Bookstore business name. Can Be used for create Dwolla receive only user.
- **dwolla_bookstore_customer_ip_address** = Bookstore customer ip address. Can Be used for create Dwolla receive only user.
- **dwolla_bookstore_customer_correlation_id** = Bookstore customer correlation id. Can Be used for create Dwolla receive only user.
- **dwolla_bookstore_funding_source_name** = Name of customer funding source. Can be any.
- **dwolla_bookstore_funding_source_routing_number** = Routing number of customer funding source.
- **dwolla_bookstore_funding_source_account_number** = Account number of customer funding source.

Mailer setup - see [Wiki / Hosting environment information / Mailtrap (Local/Dev/QA)](https://redmine.syberry.com/projects/fmt-sow227-csd-mvp-completion/wiki/Hosting_environment_information#Mailtrap-LocalDevQA)
