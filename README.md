# phila.gov

Where the pieces are pulled together for phila.gov deployment.

## Local setup

- [Install Vagrant](https://docs.vagrantup.com/v2/installation/)

- Clone this repo.
```
git clone git@github.com:CityOfPhiladelphia/phila.gov.git
```

- Initialize the virtual environment.
```
vagrant up
```

Composer relies on the GitHub API. To set it up for authentication, generate a new personal access token at https://github.com/settings/applications and run this command:

```
bin/sshc composer config github-oauth.github.com <github token>
```


### Details

The following happens when you `vagrant up`:

- Installs all server dependencies (PHP, MySQL, nginx)
- Installs tools (composer, wp-cli)
- Installs Wordpress, plugins, and theme
- Imports a database dump of settings (`wp.sql`), no data
- Imports an XML dump of content (`wp.xml`), containing Categories, Topics (custom taxonomy), Department Pages, one Information page, and one Service page

### Updating content data for import

- We recommend that you start with a clean database. You can do this by setting up a new project, or `vagrant destroy` then `vagrant up`.
- Make any changes in the Wordpress Admin. This would include adding, editing, or deleting pages or taxonomies.
- From the Wordpress Admin select `Tools -> Export`. Select the `All Content` option, then download.
- Update the contents of `wp.xml` with the new export file.
- `vagrant destroy` then `vagrant up` to test your changes.
- Commit the updated `wp.xml` to the repository


## How to deploy

1. Push tags on plugins, themes, etc.
2. In phila.gov repo, run `bin/sshc composer update`.
3. Run `bin/sshc composer install`.
4. If you don't see styles, run `bin/sshc composer install` again.
5. Check out the site locally.
6. If all looks good, commit and push.
7. Grab sha of commit from GitHub and paste into new deployment at CodeDeploy. Note that this step will go away soon!
