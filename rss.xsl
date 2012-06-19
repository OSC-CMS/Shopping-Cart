<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" />
<xsl:variable name="title" select="/rss/channel/title"/>

<xsl:template match="/">
  <html>
    <head>
      <title><xsl:value-of select="//channel/title"/></title>
      <link rel="stylesheet" href="/rss_xsl.css" type="text/css" />
    </head>
    <body>
      <xsl:apply-templates select="rss/channel"/>
    </body>
  </html>
</xsl:template>

<xsl:template match="channel">
  <div class="channel">
    <h1 class="channel"><a href="{link}" class="rssinfo"><xsl:value-of select="title"/></a></h1>
<!--
    <div class="channel_description"><xsl:value-of select="description"/></div>
    <div class="channel_copyright"><xsl:value-of select="copyright"/></div>
-->
  </div>
  <div class="content">
    <xsl:apply-templates select="item"/>
  </div>
  <div class="footerbox">
  </div>
</xsl:template>

<xsl:template match="item">
  <div class="item">
  <a clas="item" href="{link}" target="_blank" class="rssitems"><xsl:value-of select="title"/></a><br />
  <xsl:variable name="url" select="enclosure/@url" />
  <xsl:variable name="title" select="title" />
<!--
  <a href="{link}" target="_blank" class="item"><img src="{$url}" class="item" title="{$title}" alt="{$title}"/></a>
-->
  <div class="item_description"><xsl:value-of select="description" /></div>
  </div>
</xsl:template>

</xsl:stylesheet>