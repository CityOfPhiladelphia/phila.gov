Clone repos into here with something like the following:

```
git clone git@github.com:CityOfPhiladelphia/phila.gov-customization.git
```

Then, from the root of the phila.gov repo run the link script:

```
bin/repolink wp-content/plugins/phila.gov-customization
```

Now all changes made in the phila.gov-customization repo will be seen
in the context of the phila.gov project! Repos will need to be re-linked
following every composer install or update.
