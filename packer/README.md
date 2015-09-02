On an EC2 instance with packer installed execute the following command in this directory to
create an AMI:

```
sudo packer build template.json
```

Copy `user-data-sample.sh` to `user-data.sh` and modify as necessary, then create an instance:

```
aws ec2 run-instances --user-data file://user-data.sh --key-name philagov2 \
--instance-type t2.micro --associate-public-ip-address --image-id <ami-id> \
--subnet-id <vpc-subnet-id>
```
