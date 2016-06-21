<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>

  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <xsl:call-template name="head"/>
      <body>

        <div class="flex-grid">
          <div class="row">
            <xsl:call-template name="application-bar"/>
          </div>
          <div class="row">
            <xsl:call-template name="breadcrumb"/>
          </div>
          <div class="row">
            <div class="cell colspan2">
              <xsl:call-template name="toc"/>
            </div>
            <div class="cell colspan10">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <xsl:choose>
      <xsl:when test="/data/question">
        <form action=""
              method="POST">
          Are you sure that you want to delete <strong><xsl:value-of select="/data/path"/></strong>? <br/>
          This also applies to all subdirectories
          <br/>
          <br/>
          <button type="submit" value="yes" name="delete" class="button primary">Yes</button>
          <input type="hidden" name="action" value="delete"/><xsl:text> </xsl:text>
          <a class="button " href="{/data/system/baseUrl}/{/data/path}">Back</a>
        </form>
      </xsl:when>
      <xsl:otherwise>

      </xsl:otherwise>
    </xsl:choose>

  </xsl:template>


</xsl:stylesheet>