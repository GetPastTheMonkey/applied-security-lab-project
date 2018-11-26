Applied Security Lab Project
============================

Applied Security Lab Project of Tobias, Adi and Sven which is to hand in until 22.11.2018

Folder Information
------------------

Here is a list of "what is where"

Directories:

  * `admin`: Source code of the admin interface. Copied to `/var/www/html/admin.imovie.local` during setup of the `webapp` server
  * `certdata`: Source code of the certdata API. Copied to `/var/www/html/certdata.api.imovie.local` during setup of the `certdata` server
  * `certs`: Contains the certificates, private keys and corresponding encryption keys for all URLs. The needed files are copied to `/etc/ssl` during any server setup. See [the certificate README](certs/README.md) for more information
  * `configs`: Contains the common `hosts` file as well as all apache virtualhost config files for all URLs. The needed virtualhost config files are copied to `/etc/apache2/sites-available` during any server setup
  * `core-ca`: Source code of the core CA API. Copied to `/var/www/html/ca.api.imovie.local` during setup of the `coreca` server
  * `database`: Contains the database setup files for the certificate database as well as the user database. A file is executed using `mysql < database/filename.sql`, if needed during a server setup
  * `ssh`: Contains the SSH keys for the backup script. `id_rsa` and `id_rsa.pub` are copied to `/home/backup_user/.ssh` during setup of the `userdata` server. `authorized_keys` is copied to `/home/user/.ssh` during any server setup
  * `userdata`: Source code of the userdata API. Copied to `/var/www/html/userdata.api.imovie.local` during setup of the `userdata` server
  * `webapp`: Source code of the standard web application. Copied to `/var/www/html/www.imovie.local` during setup of the `webapp` server

Files:

  * `backup_script.php`: Backup script that is run every 10 minutes on the `userdata` server. Copied to `/home/backup_user` during setup of the `userdata` server
  * `pw_hasher.php`: Small password hashing script to hash strings with sha512 with a pseudorandom salt
  * `README.md`: Hey, you are reading this right now!
  * `server_setup.php`: Setup script to automatically set up a server that you would like. See below for more information


How To Setup a Server
---------------------

It is very simple to setup a server. Follow these steps:

  * Go to the [Ubuntu Server Download Page](https://www.ubuntu.com/download/server) and download the Ubuntu Server 18.04.1 LTS
  * Open VirtualBox and create a new virtual machine for 64-bit Ubuntu
  * Select the downloaded `.iso` file as booting medium
  * Follow the installation guide of Ubuntu
  * After restarting the machine, log in to the account you just created
  * Download PHP 7.2 by running `sudo apt-get install php`
  * Clone this Git repository by running `git clone https://gitlab.vis.ethz.ch/app_sec_lab/applied-security-lab-project`
  * `cd`into the repo by running `cd applied-security-lab-project`
  * Decide what server you would like to set up. There are four possibilities:
    * `webapp`: The web frontend for users and administrators
    * `coreca`: The Core CA server
    * `userdata`: The user database and API
    * `certdata`: Certificate database and API
  * Run `sudo php server_setup.php <servertype>`
  * If all goes well, you may then change the VirtualBox network settings to use an internal network (you don't have to change the netplan settings inside the virtual machine - this is automatically done by the setup script)
  * Have fun :)

