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
            <div class="cell colspan10 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
        </div>
        <xsl:call-template name="dialog"/>
        <script type="text/javascript">
          initEdituser();
        </script>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">
    <xsl:choose>
      <xsl:when test="/data/mode ='list'">
        <xsl:call-template name="list"/>
      </xsl:when>
      <xsl:when test="/data/mode ='edit'">
        <xsl:call-template name="edit"/>
      </xsl:when>
      <xsl:when test="/data/mode ='register'">
        <xsl:call-template name="register"/>
      </xsl:when>
    </xsl:choose>

  </xsl:template>

  <xsl:template name="list">
    <table class="table">
      <xsl:for-each select="/data/user/item">
        <xsl:variable name="cssClass">
          <xsl:choose>
            <xsl:when test="active='1'">
              success
            </xsl:when>
            <xsl:otherwise>
              error
            </xsl:otherwise>
          </xsl:choose>
        </xsl:variable>

        <tr class="{$cssClass}">
          <td>
            <xsl:value-of select="displayName"/>
          </td>
          <td>
            <xsl:value-of select="email"/>
          </td>
          <td>
            <a href="{/data/system/baseUrl}{/data/system/requestUri}/{id}/edit">
              <span class="mif-pencil"></span>
            </a>
            <xsl:text> </xsl:text>
            <xsl:choose>
              <xsl:when test="active='1'">
                <a href="{/data/system/baseUrl}{/data/system/requestUri}/{id}/inactivate" class="inactive">
                  <span class="mif-blocked"></span>
                </a>
                <xsl:text> </xsl:text>
              </xsl:when>
              <xsl:otherwise>
                <a href="{/data/system/baseUrl}{/data/system/requestUri}/{id}/activate" class="activate">
                  <span class="mif-checkmark"></span>
                </a>
                <xsl:text> </xsl:text>
              </xsl:otherwise>
            </xsl:choose>
            <a href="{/data/system/baseUrl}{/data/system/requestUri}/{id}/delete" class="delete">
              <span class="mif-cross"></span>
            </a>

          </td>
        </tr>

      </xsl:for-each>
    </table>
  </xsl:template>

  <xsl:template name="edit">
    <form action="" method="POST" id="editForm">
      <div class="grid">
        <div class="row">
          <div class="cell">
            <label>Displayname </label>
            <div class="input-control text">
              <input type="text" name="displayname" value="{/data/userdata/displayName}" placeholder="displayname"/>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="cell">
            <label>E-Mail </label>
            <div class="input-control text">
              <input type="text" name="email" value="{/data/userdata/email}" placeholder="email"/>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="cell">
            <label>Password </label>
            <div class="input-control password" data-role="input">
              <input type="password" name="password"/>
              <button class="button helper-button reveal">
                <span class="mif-looks"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="cell">


            <label class="input-control checkbox small-check">
              <span class="caption">Admin</span>

              <xsl:choose>
                <xsl:when test="/data/userdata/group ='1'">
                  <input type="checkbox" name="group"
                         value="1" checked="checked"/>
                </xsl:when>
                <xsl:otherwise>
                  <input type="checkbox" name="group"
                         value="1"/>
                </xsl:otherwise>
              </xsl:choose>

              <span class="check"></span>
            </label>

          </div>
        </div>
      </div>


      <input type="hidden" name="action" value="edit"/>
      <button type="submit" class="button primary" id="saveUser">Save</button>
      <xsl:text> </xsl:text>
      <a href="{/data/system/baseUrl}globalconfig#user" class="button">Back</a>
    </form>

  </xsl:template>
  <xsl:template name="register">

    <form action="" method="POST" id="editForm">
      <div class="grid">
        <div class="row">
          <div class="cell">
            <label>Displayname </label>
            <div class="input-control text">
              <input type="text" name="displayname" value="" placeholder="displayname"/>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="cell">
            <label>E-Mail </label>
            <div class="input-control text">
              <input type="text" name="email" value="" placeholder="email"/>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="cell">
            <label>Password </label>
            <div class="input-control password" data-role="input">
              <input type="password" name="password"/>
              <button class="button helper-button reveal">
                <span class="mif-looks"></span>
              </button>
            </div>
          </div>
        </div>
      </div>


      <input type="hidden" name="action" value="edit"/>
      <button type="submit" class="button primary" id="saveUser">Save</button>
      <xsl:text> </xsl:text>
      <a href="{/data/system/baseUrl}globalconfig#user" class="button">Back</a>
    </form>


  </xsl:template>

  <xsl:template name="dialog">
    <div data-role="dialog" id="dialog" class="padding20">
      <h4 class="dialogTitle"></h4>
      <p class="dialogContent"></p>
      <button class="button primary" id="dialogButton">Ok</button>
    </div>
  </xsl:template>


</xsl:stylesheet>