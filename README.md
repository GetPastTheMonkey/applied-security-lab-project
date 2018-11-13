Applied Security Lab Project
============================

Applied Security Lab Project of Tobias, Adi and Sven which is to hand in until 22.11.2018

How To Setup a Server
---------------------

It is very simple to setup a server. Follow these steps:

  * Go to the [Ubuntu Server Download Page](https://www.ubuntu.com/download/server) and download the Ubuntu Server 18.04.1 LTS
  * Open VirtualBox and create a new virtual machine for 64-bit Ubuntu
  * Select the downloaded `.iso` file as booting medium
  * Follow the installation guide of Ubuntu
  * After restarting the machine, log in to the account you just created
  * Download PHP 7.2 by running `sudo apt-get install php7.2`
  * Clone this Git repository by running `git clone https://gitlab.vis.ethz.ch/app_sec_lab/applied-security-lab-project
  * `cd`into the repo by running `cd applied-security-lab-project`
  * Decide what server you would like to set up. There are four possibilities:
    * `webapp`: The web frontend for users and administrators
    * `coreca`: The Core CA server
    * `userdata`: The user database and API
    * `certdata`: Certificate database and API
  * Run `sudo php server_setup.php <servertype>`
  * If all goes well, you may then change the VirtualBox network settings to use an internal network (you don't have to change the netplan settings inside the virtual machine - this is automatically done by the setup script)
  * Have fun :)

