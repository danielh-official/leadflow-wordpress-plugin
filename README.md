# LeadFlow Client Enquiry Manager

LeadFlow is a portfolio WordPress plugin for service businesses. It renders a quote-request form, validates submissions, stores enquiries as private WordPress records, and gives administrators a practical review screen.

## Live demo

Open the isolated browser demo:

https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/danielh-official/leadflow-wordpress-plugin/main/blueprint.json

Each visitor receives a separate WordPress Playground instance. Demo changes are not shared with other visitors and should not be treated as production data.

## Local development

Requirements: Docker Desktop, Node.js with npm, Git, and @wordpress/env 11.15.0.

```sh
npm -g i @wordpress/env@11.15.0
wp-env start
```

Open http://localhost:8888 and sign in at http://localhost:8888/wp-admin with username `admin` and password `password`.

Stop the environment with:

```sh
wp-env stop
```

## Architecture

- `leadflow.php` loads the plugin components.
- `Post_Type` registers private `lf_lead` records with a WordPress admin interface.
- `Form` renders `[leadflow_form]`, validates public submissions, and persists sanitized metadata.
- `Admin` adds useful list columns and a read-only enquiry detail box.

## Security decisions

- Submitted values are unslashed, sanitized by field type, validated, and escaped on output.
- The form uses a WordPress nonce as an intent check.
- Anonymous nonces are not authentication or complete spam protection.
- Redirects use the WordPress safe-redirect API.
- Lead records are not publicly queryable or exposed through the REST API.

## Installable ZIP

On the GitHub repository page, select Code, then Download ZIP. In WordPress, use Plugins > Add New > Upload Plugin > Install Now > Activate Plugin.

## Shortcode

```text
[leadflow_form]
```

## License

GPL-2.0-or-later
