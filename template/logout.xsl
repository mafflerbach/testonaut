<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>
  <xsl:import href="form.xsl"/>


  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <xsl:call-template name="head"/>
      <body class="bg-darkTeal">
        <xsl:call-template name="message">
          <xsl:with-param name="title">Logout</xsl:with-param>
        </xsl:call-template>

      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>