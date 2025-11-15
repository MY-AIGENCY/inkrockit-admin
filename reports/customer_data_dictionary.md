# Customer Data Dictionary

Only columns that currently contain at least one non-null (and, for text fields, non-empty) value are listed.

## `credit_card`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `user_id` | `int` | NO |  |
| `title` | `varchar(200)` | NO |  |
| `card_number` | `varchar(20)` | NO |  |
| `ccv` | `varchar(4)` | NO |  |
| `exp_date` | `varchar(10)` | NO |  |
| `full_card_name` | `varchar(150)` | NO |  |
| `full_user_name` | `varchar(150)` | NO |  |
| `billing_id` | `int` | NO |  |
| `shipping_id` | `int` | NO |  |
| `default` | `int` | NO |  |
| `view_as` | `varchar(255)` | NO |  |
| `bill_company` | `varchar(150)` | NO |  |
| `bill_fname` | `varchar(150)` | NO |  |
| `bill_lname` | `varchar(150)` | NO |  |
| `bill_address` | `varchar(255)` | NO |  |
| `bill_address2` | `varchar(255)` | NO |  |
| `bill_city` | `varchar(150)` | NO |  |
| `bill_state` | `varchar(150)` | NO |  |
| `bill_zip` | `varchar(150)` | NO |  |
| `bill_country` | `varchar(150)` | NO |  |
| `bill_email` | `varchar(150)` | NO |  |
| `bill_phone` | `varchar(150)` | NO |  |
| `bill_phone_ext` | `varchar(150)` | NO |  |
| `bill_suite` | `varchar(150)` | NO |  |

## `credit_card_billing`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `title` | `varchar(200)` | NO |  |
| `user_id` | `int` | NO |  |
| `first_name` | `varchar(100)` | NO |  |
| `last_name` | `varchar(100)` | NO |  |
| `address` | `text` | NO |  |
| `city` | `varchar(100)` | NO |  |
| `state` | `varchar(100)` | NO |  |
| `zip` | `varchar(50)` | NO |  |
| `country` | `varchar(200)` | NO |  |
| `email` | `varchar(200)` | NO |  |
| `phone` | `varchar(200)` | NO |  |
| `phone_ext` | `varchar(10)` | NO |  |
| `company` | `varchar(255)` | YES |  |
| `visible` | `int` | NO |  |
| `default` | `int` | NO |  |
| `suite` | `text` | NO |  |
| `address2` | `text` | NO |  |
| `full_name` | `varchar(255)` | NO |  |

## `credit_card_shipping`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `user_id` | `int` | NO |  |
| `company_id` | `int` | NO |  |
| `title` | `varchar(200)` | NO |  |
| `first_name` | `varchar(200)` | NO |  |
| `last_name` | `varchar(200)` | NO |  |
| `company` | `varchar(200)` | YES |  |
| `address` | `text` | NO |  |
| `address2` | `text` | NO |  |
| `city` | `varchar(40)` | NO |  |
| `state` | `varchar(40)` | NO |  |
| `zip` | `varchar(40)` | NO |  |
| `country` | `varchar(100)` | NO |  |
| `phone` | `varchar(100)` | NO |  |
| `phone_ext` | `varchar(5)` | NO |  |
| `email` | `varchar(100)` | NO |  |
| `public` | `int` | NO |  |

## `events`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `date` | `datetime` | NO |  |
| `type` | `varchar(50)` | NO |  |
| `text` | `text` | NO |  |
| `uid` | `int` | NO |  |
| `type_id` | `int` | NO |  |

## `eye_user_company`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `uid` | `int` | NO |  |
| `company_id` | `int` | NO |  |

## `message_inbox`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `date` | `varchar(100)` | NO |  |
| `subject` | `varchar(255)` | NO |  |
| `from` | `varchar(255)` | NO |  |
| `to` | `varchar(100)` | NO |  |
| `text` | `text` | NO |  |
| `job_id` | `int` | NO |  |

## `payment_history`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `job_id` | `int` | NO |  |
| `type` | `varchar(20)` | NO |  |
| `user_type` | `varchar(3)` | NO |  |
| `date` | `datetime` | NO |  |
| `client_id` | `int` | NO |  |
| `summ` | `float` | NO |  |
| `description` | `text` | NO |  |
| `card_id` | `int` | YES |  |
| `edg` | `int` | NO |  |
| `transaction_code` | `varchar(100)` | NO |  |
| `removed` | `int` | NO |  |
| `procent` | `int` | NO |  |
| `total` | `varchar(10)` | NO |  |
| `small_descr` | `varchar(10)` | NO |  |

## `request_note_required`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `note_id` | `int` | NO |  |
| `from_uid` | `int` | NO |  |
| `for_uid` | `int` | NO |  |
| `text` | `text` | NO |  |
| `date` | `datetime` | NO |  |
| `status` | `int` | NO |  |

## `request_notes`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `request_id` | `int` | NO |  |
| `company_id` | `int` | NO |  |
| `text` | `text` | NO |  |
| `date` | `datetime` | NO |  |
| `job_id` | `int` | NO |  |
| `author_id` | `int` | NO |  |
| `type` | `varchar(20)` | NO |  |
| `type_user` | `varchar(3)` | NO |  |
| `removed` | `int` | NO |  |
| `required_uid` | `int` | NO |  |

## `request_samples`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `request_id` | `int` | NO |  |
| `industry_id` | `int` | NO |  |
| `industry_samples` | `text` | NO |  |

## `request_samples_collection`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `title` | `text` | NO |  |
| `elements` | `text` | NO |  |

## `requests`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `job_id` | `int` | YES |  |
| `user_id` | `int` | YES |  |
| `company_id` | `int` | NO |  |
| `request_date` | `date` | YES |  |
| `operating_sys` | `text` | YES |  |
| `graphics_app` | `text` | YES |  |
| `ref_source` | `text` | YES |  |
| `other_source` | `text` | YES |  |
| `processed_date` | `date` | YES |  |
| `industry` | `text` | YES |  |
| `industry_send` | `text` | NO |  |
| `conversations` | `text` | YES |  |
| `complete_address` | `text` | YES |  |
| `search_id` | `text` | YES |  |
| `offers` | `text` | YES |  |
| `order_data` | `text` | YES |  |
| `tracking_number` | `text` | YES |  |
| `search_keyword` | `text` | YES |  |
| `user_ip` | `text` | YES |  |
| `status` | `int` | NO |  |

## `requests_more_sent`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `req_id` | `int` | NO |  |
| `processed_date` | `date` | NO |  |
| `tracking_number` | `varchar(100)` | NO |  |

## `user_additional_info`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `uid` | `int` | NO |  |
| `type` | `enum('email','phone')` | NO |  |
| `value` | `varchar(100)` | NO |  |
| `content_type` | `varchar(10)` | NO |  |

## `user_jobs`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `user_id` | `int` | NO |  |
| `company_id` | `int` | NO |  |
| `job_id` | `varchar(50)` | NO |  |
| `estimate_id` | `varchar(50)` | NO |  |
| `order_total` | `float` | NO |  |
| `payments` | `float` | NO |  |
| `order_counts` | `int` | NO |  |
| `edg` | `int` | NO |  |

## `users`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `login` | `varchar(100)` | NO |  |
| `password` | `varchar(50)` | NO |  |
| `email` | `varchar(100)` | NO |  |
| `email_alt` | `varchar(100)` | NO |  |
| `first_name` | `varchar(100)` | NO |  |
| `last_name` | `varchar(100)` | NO |  |
| `group_id` | `int` | NO |  |
| `user_abbr` | `varchar(10)` | NO |  |
| `company_id` | `varchar(255)` | NO |  |
| `country` | `varchar(100)` | NO |  |
| `street` | `varchar(255)` | NO |  |
| `street2` | `varchar(255)` | NO |  |
| `city` | `varchar(100)` | NO |  |
| `state` | `varchar(100)` | NO |  |
| `zipcode` | `varchar(20)` | NO |  |
| `phone` | `varchar(100)` | NO |  |
| `phone_ext` | `varchar(10)` | NO |  |
| `phone_type` | `varchar(10)` | NO |  |
| `position` | `varchar(255)` | NO |  |
| `industry` | `varchar(255)` | NO |  |
| `fax` | `varchar(100)` | NO |  |
| `admin_comment` | `varchar(255)` | NO |  |

## `users_company`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `id` | `int` | NO |  |
| `company` | `varchar(255)` | NO |  |
| `main_uid` | `int` | NO |  |
| `abbr` | `varchar(10)` | NO |  |
| `duplicate` | `int` | NO |  |

## `users_company_exec`

| Column | SQL Type | Nullable | Notes |
| --- | --- | --- | --- |
| `company_id` | `int` | NO |  |
| `abbr` | `varchar(20)` | NO |  |
