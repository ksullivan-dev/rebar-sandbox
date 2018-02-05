This is all going to change a good deal with the rebuild, so leaving this blank for now.

To get this going as a Vagrant VM:

```
cp Vagrantfile.local Vagrantfile
vagrant up
```

Additionally, you will need to edit your hosts file so that this can be accessed via a specific URL by adding the
following line to your /etc/hosts file:

```
127.0.0.1 local.api.sandbox.com
```

The API should then be available at: http://local.api.sandbox.com:8765/v1/
