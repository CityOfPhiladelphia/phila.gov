import csvkit
from slugify import slugify

f = open('topics.csv')
reader = csvkit.CSVKitReader(f)
i = 112
headers = ['name', 'parent', 'description']

for l in reader:
    values = {header: l[i] for (i, header) in enumerate(headers)}
    values['slug'] = slugify(l[0])
    values['parent_slug'] = slugify(l[1])
    values['index'] = i

    cat_xml = """
<wp:term>
  <wp:term_id>%(index)s</wp:term_id>
  <wp:term_taxonomy>topics</wp:term_taxonomy>
  <wp:term_slug>%(slug)s</wp:term_slug>
  <wp:term_parent>%(parent_slug)s</wp:term_parent>
  <wp:term_name><![CDATA[%(name)s]]></wp:term_name>
  <wp:term_description><![CDATA[%(description)s]]></wp:term_description>
</wp:term>
    """ % (values)
    print cat_xml.encode('utf-8').strip()
    i = i + 1
