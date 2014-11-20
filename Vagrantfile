Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provider "lxc" do |lxc, override|
    override.vm.box = "fgrehm/trusty64-lxc"
  end
  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
  end
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 8000, host: 8000
  config.vm.provision "shell", path: "bootstrap.sh"
end
