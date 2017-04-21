# Trouble Shooting

1. HTTP error 500 (Internal Server Error) on some `.php` file.

    This may be caused by permissions on cPanel. Apply the following command on the file that returns error code 500.
    
    ```bash
    $ chmod 755 *.php
    ```