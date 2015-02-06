import csvkit
from slugify import slugify

f = open('info.csv')
reader = csvkit.CSVKitReader(f)
i = 1
headers = ['title']

for l in reader:
    values = {header: l[i] for (i, header) in enumerate(headers)}
    values['slug'] = slugify(l[0])
    values['index'] = i

    page_xml = """
  <item>
    <title>%(title)s</title>
    <link>http://philagov-staging-1019513781.us-east-1.elb.amazonaws.com/%(slug)s/</link>
    <pubDate>Mon, 08 Dec 2014 18:41:17 +0000</pubDate>
    <dc:creator><![CDATA[admin]]></dc:creator>
    <guid isPermaLink="false">http://ec2-54-205-250-197.compute-1.amazonaws.com/?p=%(index)d</guid>
    <description></description>
    <content:encoded><![CDATA[]]></content:encoded>
    <excerpt:encoded><![CDATA[]]></excerpt:encoded>
    <wp:post_id>%(index)d</wp:post_id>
    <wp:post_date>2014-12-08 13:41:17</wp:post_date>
    <wp:post_date_gmt>2014-12-08 18:41:17</wp:post_date_gmt>
    <wp:comment_status>open</wp:comment_status>
    <wp:ping_status>open</wp:ping_status>
    <wp:post_name>%(slug)s</wp:post_name>
    <wp:status>publish</wp:status>
    <wp:post_parent>0</wp:post_parent>
    <wp:menu_order>0</wp:menu_order>
    <wp:post_type>post</wp:post_type>
    <wp:post_password></wp:post_password>
    <wp:is_sticky>0</wp:is_sticky>
    <wp:postmeta>
      <wp:meta_key>_edit_last</wp:meta_key>
      <wp:meta_value><![CDATA[1]]></wp:meta_value>
    </wp:postmeta>
    <wp:postmeta>
      <wp:meta_key>_wp_old_slug</wp:meta_key>
      <wp:meta_value><![CDATA[%(slug)s]]></wp:meta_value>
    </wp:postmeta>
  </item>
    """ % (values)
    print page_xml.encode('utf-8').strip()

    i = i + 1
