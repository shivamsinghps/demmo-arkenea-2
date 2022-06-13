parameters:
    is_prod: ##IS_PROD##
    build_number: ##BUILD_NUMBER##
    domain_name: ##DOMAIN_NAME##
    database_host: ##DATABASE_HOST##
    database_port: ##DATABASE_PORT##
    database_name: ##DATABASE_NAME##
    database_user: ##DATABASE_USER##
    database_password: ##DATABASE_PASSWORD##
    mailer_transport: ##MAILER_TRANSPORT##
    mailer_host: ##MAILER_HOST##
    mailer_port: ##MAILER_PORT##
    mailer_user: ##MAILER_USER##
    mailer_password: ##MAILER_PASSWORD##
    sender_address: ##SENDER_EMAIL##
    sender_name: ##SENDER_NAME##
    secret: ##CSRF_SECRET##
    node_bin: ##NODE_BIN##
    node_paths: ##NODE_PATH##
    npm_bin: ##NODE_BIN##
    cache_server: ##CACHE_SERVER##
    cache_adapter: ##CACHE_ADAPTER##
    lock_adapter: ##LOCK_ADAPTER##

    # Marketing params
    marketing_app_url: ##MARKETING_APP_URL##
    base_menu__help: ##MARKETING_HELP_PAGE##
    base_menu__about: ##MARKETING_ABOUT_PAGE##
    base_menu__privacy: ##MARKETING_PRIVACY_PAGE##
    base_menu__how_works: ##MARKETING_HOW_WORKS_PAGE##
    base_menu__terms: ##MARKETING_TERMS_PAGE##
    base_menu__fees: ##MARKETING_FEES_PAGE##
    base_menu__partner: ##MARKETING_PARTNER_WITH_FMT_PAGE##

    # Calculation settings
    calculate_fmt_fee_percent: ##CALCULATE_FMT_FEE_PERCENT##
    calculate_stripe_fee_percent: ##CALCULATE_STRIPE_FEE_PERCENT##
    calculate_stripe_fee_static: ##CALCULATE_STRIPE_FEE_STATIC##

    # Third-party services
    nebook_endpoint: ##NEBOOK_ENDPOINT##
    nebook_wsdl: ##NEBOOK_WSDL##
    nebook_xmlns: ##NEBOOK_XMLNS##
    nebook_bookstore_id: ##NEBOOK_BOOKSTORE_ID##
    nebook_user: ##NEBOOK_USER##
    nebook_password: ##NEBOOK_PASSWORD##
    nebook_cache_lifetime: ##NEBOOK_CACHE_LIFETIME##

    # Stripe settings
    stripe_public_key: ##STRIPE_PUBLIC_KEY##
    stripe_secret_key: ##STRIPE_SECRET_KEY##
    stripe_live_mode: ##STRIPE_LIVE_MODE##
    stripe_webhook_signature: ##STRIPE_WEBHOOK_SIGNATURE##

    # Content storage settings
    avatar_storage: ##AVATAR_STORAGE##
    s3_key: ##S3_KEY##
    s3_secret: ##S3_SECRET##

    # AWS
    aws_region: ##AWS_REGION##
    aws_key: ##AWS_KEY##
    aws_secret: ##AWS_SECRET##

    # Google analytics
    google_analytics_id: ##GOOGLE_ANALYTICS_ID##

    # Socials
    social_facebook_link: ##SOCIAL_FACEBOOK_LINK##
    social_twitter_link: ##SOCIAL_TWITTER_LINK##
    social_linkedin_link: ##SOCIAL_LINKEDIN_LINK##
    social_instagram_link: ##SOCIAL_INSTAGRAM_LINK##

    # Dwolla
    dwolla_endpoint: ##DWOLLA_ENDPOINT##
    dwolla_client_id: ##DWOLLA_CLIENT_ID##
    dwolla_client_key: ##DWOLLA_CLIENT_KEY##
    fmt.payments.dwolla.webhooks_token: ##DWOLLA_WEBHOOKS_TOKEN##
    dwolla_webhooks_endpoint: ##DWOLLA_WEBHOOKS_ENDPOINT##
    dwolla_fmt_funding_source: ##DWOLLA_FMT_FUNDING_SOURCE##
    dwolla_bookstore_email: ##DWOLLA_BOOKSTORE_EMAIL##
    dwolla_bookstore_customer_first_name: ##DWOLLA_BOOKSTORE_CUSTOMER_FIRST_NAME##
    dwolla_bookstore_customer_last_name: ##DWOLLA_BOOKSTORE_CUSTOMER_LAST_NAME##
    dwolla_bookstore_customer_email: ##DWOLLA_BOOKSTORE_CUSTOMER_EMAIL##
    dwolla_bookstore_customer_business_name: ##DWOLLA_BOOKSTORE_CUSTOMER_BUSINESS_NAME##
    dwolla_bookstore_customer_ip_address: ##DWOLLA_BOOKSTORE_CUSTOMER_IP_ADDRESS##
    dwolla_bookstore_customer_correlation_id: ##DWOLLA_BOOKSTORE_CUSTOMER_CORRELATION_ID##
    dwolla_bookstore_funding_source_name: ##DWOLLA_BOOKSTORE_FUNDING_SOURCE_NAME##
    dwolla_bookstore_funding_source_routing_number: ##DWOLLA_BOOKSTORE_FUNDING_SOURCE_ROUTING_NUMBER##
    dwolla_bookstore_funding_source_account_number: ##DWOLLA_BOOKSTORE_FUNDING_SOURCE_ACCOUNT_NUMBER##
