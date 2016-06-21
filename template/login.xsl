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
        <div class="uk-width-medium-1-2 uk-container-center">
          <xsl:call-template name="login-form"/>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>