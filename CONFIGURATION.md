# Configuration

This site is the existing PHP and HTML application. Pages are served as files. Database credentials are not stored in the repository.

`config.php` opens a MySQL connection with `getenv`:

- `DB_HOST`
- `DB_USER`
- `DB_PASSWORD`
- `DB_NAME`

Set all four in the server environment before using `send_enquiry.php` or `mypltdata2022.php`. No database dump or schema was included with the source, so those scripts are not wired to a production database from this repository.

`send_enquiry.php` inserts a posted enquiry into `enquires` (`name`, `email`, `mobile`, `course`, `created_on`). `mypltdata2022.php` exports that table as a tab-separated download. `send_contact_mail.php` sends the posted contact fields with PHP `mail()` to the address already present in that script. Mail delivery depends on the server's mail transport. Do not point these scripts at production data or a live mailbox for tests.

`.env.example` lists the variable names only.
