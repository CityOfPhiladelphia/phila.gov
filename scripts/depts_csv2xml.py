import csvkit
from slugify import slugify

f = open('departments.csv')
reader = csvkit.CSVKitReader(f)
i = 1
headers = ['short', 'full', 'url', 'topics', 'description']

for l in reader:
    values = {header: l[i] for (i, header) in enumerate(headers)}
    values['slug'] = slugify(l[0])
    values['index'] = i

    cat_xml = """
<wp:category>
  <wp:term_id>%(index)s</wp:term_id>
  <wp:category_nicename>%(slug)s</wp:category_nicename>
  <wp:category_parent/>
  <wp:cat_name><![CDATA[%(short)s]]></wp:cat_name>
  <wp:category_description><![CDATA[]]></wp:category_description>
</wp:category>
    """ % (values)
    print cat_xml.encode('utf-8').strip()

    page_xml = """
<item>
  <title>%(full)s</title>
  <link>http://localhost:19102/departments/%(slug)s/</link>
  <pubDate>Fri, 21 Nov 2014 14:35:07 +0000</pubDate>
  <dc:creator>admin</dc:creator>
  <guid isPermaLink="false">http://localhost:8080/?post_type=department_page&amp;p=%(index)d</guid>
  <description/>
  <content:encoded><![CDATA[]]></content:encoded>
  <excerpt:encoded><![CDATA[]]></excerpt:encoded>
  <wp:post_id>%(index)d</wp:post_id>
  <wp:post_date>2014-11-21 14:35:07</wp:post_date>
  <wp:post_date_gmt>2014-11-21 14:35:07</wp:post_date_gmt>
  <wp:comment_status>closed</wp:comment_status>
  <wp:ping_status>closed</wp:ping_status>
  <wp:post_name>%(slug)s</wp:post_name>
  <wp:status>publish</wp:status>
  <wp:post_parent>0</wp:post_parent>
  <wp:menu_order>0</wp:menu_order>
  <wp:post_type>department_page</wp:post_type>
  <wp:post_password/>
  <wp:is_sticky>0</wp:is_sticky>
  <category domain="category" nicename="%(slug)s"><![CDATA[%(short)s]]></category>
  <wp:postmeta>
    <wp:meta_key>phila_dept_url</wp:meta_key>
    <wp:meta_value><![CDATA[%(url)s]]></wp:meta_value>
  </wp:postmeta>
  <wp:postmeta>
    <wp:meta_key>phila_dept_desc</wp:meta_key>
    <wp:meta_value><![CDATA[%(description)s]]></wp:meta_value>
  </wp:postmeta>
</item>
    """ % (values)
    print page_xml.encode('utf-8').strip()

    i = i + 1
