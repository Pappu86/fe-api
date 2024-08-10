{!! '<'.'?'.'xml version="1.0" encoding="UTF-8" ?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <lastBuildDate>{{$feed->lastBuildDate}}</lastBuildDate>

        <title>{{$feed->title}}</title>

        <description>{{$feed->description}}</description>

        <atom:link href="{{$feed->link}}" rel="self"></atom:link>

        <link>{{$feed->link}}</link>

        @foreach($items as $post)
        <item>

            <guid isPermaLink="true">{{$post['slug']}}</guid>

            <pubDate>{{$post['published_at']}}</pubDate>

            <title>{{$post['title']}}</title>

            <description><![CDATA[{{$post['description']}}]]></description>

            <content:encoded>

                <![CDATA[{!! $post['content'] !!}]]>

            </content:encoded>

            <link>{{$post['link']}}</link>

            <author>editor@thefinancialexpress.com.bd ({{$post['author']}})</author>

            <media:content url="{{$post['image']}}" expression="full" width="800" height="600">

                <media:description type="plain">

                    <![CDATA[{{$post['caption']}}]]>

                </media:description>

            </media:content>

        </item>
        @endforeach
    </channel>
</rss>