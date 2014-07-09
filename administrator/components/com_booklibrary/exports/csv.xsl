<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" encoding="utf-8"/>
	<xsl:template match="/">
		<xsl:for-each select="data/books/book">
			<xsl:value-of select="bookid"/><xsl:text>|</xsl:text>
			<xsl:value-of select="isbn"/><xsl:text>|</xsl:text>
			<xsl:value-of select="title"/><xsl:text>|</xsl:text>
			<xsl:value-of select="authors"/><xsl:text>|</xsl:text>
			<xsl:value-of select="manufacturer"/><xsl:text>|</xsl:text>
			<xsl:value-of select="releasedate"/><xsl:text>|</xsl:text>
			<xsl:value-of select="language"/><xsl:text>|</xsl:text>
			<xsl:value-of select="hits"/><xsl:text>|</xsl:text>
			<xsl:value-of select="rating"/><xsl:text>|</xsl:text>
			<xsl:value-of select="price"/><xsl:text>|</xsl:text>
			<xsl:value-of select="url"/><xsl:text>|</xsl:text>
			<xsl:value-of select="imageURL"/><xsl:text>|</xsl:text>
			<xsl:value-of select="edition"/><xsl:text>|</xsl:text>
			<xsl:value-of select="ebookURL"/><xsl:text>|</xsl:text>
			<xsl:value-of select="informationFrom"/><xsl:text>|</xsl:text>
			<xsl:value-of select="date"/><xsl:text>|</xsl:text>
      <xsl:value-of select="priceunit"/><xsl:text>|</xsl:text>
      <xsl:value-of select="owneremail"/><xsl:text>|</xsl:text>
      <xsl:value-of select="featured_clicks"/><xsl:text>|</xsl:text>
      <xsl:value-of select="featured_shows"/><xsl:text>|</xsl:text>
      <xsl:value-of select="numberOfPages"/><xsl:text>|</xsl:text>
			<xsl:value-of select="comment"/><xsl:text>&#xA;</xsl:text>
                        
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>

