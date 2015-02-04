Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provider "lxc" do |lxc, override|
    override.vm.box = "fgrehm/trusty64-lxc"
  end
  config.vm.network "forwarded_port", guest: 80, host: 19102
  config.vm.provision "shell", path: "bootstrap.sh"
end
