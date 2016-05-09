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
          <div class="row auto-size ">
            <div class="cell size-p100 padding20">
              <xsl:call-template name="content"/>
            </div>
          </div>
          <div class="row auto-size ">
            <div class="cell size-p100 padding20" id="editform">
            </div>
          </div>
        </div>

      </body>
    </html>
  </xsl:template>

  <xsl:template name="content">

    <form class="compForm" name="compForm"
          method="post"
          action="">
      <div id="toolbar"
           style="display: none;">
        <a data-wysihtml5-command="bold"
           title="CTRL+B"><span class="mif-bold"></span></a> |
        <a data-wysihtml5-command="italic"
           title="CTRL+I"><span class="mif-italic"></span></a> |
        <a data-wysihtml5-command="underline"
           title="CTRL+U"><span class="mif-underline"></span></a> |
        <a data-wysihtml5-command="createLink"><span class="mif-link"></span></a> |
        <a data-wysihtml5-command="insertImage"><span class="mif-image"></span></a> |
        <a data-wysihtml5-command="uploadFile">
          <span class="mif-upload"></span>
        </a> |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h1"><span class="fa fa-header">H1</span></a> |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h2"><span class="fa fa-header">H2</span></a> |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h3"><span class="fa fa-header">H3</span></a> |
        <a data-wysihtml5-command="formatBlock"
           data-wysihtml5-command-value="h4"><span class="fa fa-header">H4</span></a> |
        <a data-wysihtml5-command="insertUnorderedList"><span class="mif-list"></span></a> |
        <a data-wysihtml5-command="insertOrderedList"><span class="mif-list-numbered"></span></a> |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="red">red</a> |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="green">green</a> |
        <a data-wysihtml5-command="foreColor"
           data-wysihtml5-command-value="blue">blue</a> |
        <a data-wysihtml5-action="change_view"><span class="mif-file-code"></span></a>

        <div data-wysihtml5-dialog="createLink"
             class="editorBox"
             style="display: none;">
          <label>
            Link:
            <input data-wysihtml5-dialog-field="href"
                   value="http://"
                   type="text"/>
          </label>
          <a data-wysihtml5-dialog-action="save"
             class="btn  btn-primary">OK</a> <a data-wysihtml5-dialog-action="cancel"
                                                     class="btn btn-link">Cancel</a>
          <a data-wysihtml5-dialog-action="search"
                   class="button primary">Search</a>
        </div>

        <div data-wysihtml5-dialog="insertImage"
             class="editorBox"
             style="display: none;">
          <label>
            Image:
            <input data-wysihtml5-dialog-field="src"
                   value="http://"
                   type="text"/>
          </label>
          <label>
            Align:
            <select data-wysihtml5-dialog-field="className">
              <option value="">default</option>
              <option value="wysiwyg-float-left">left</option>
              <option value="wysiwyg-float-right">right</option>
            </select>
          </label>
          <a data-wysihtml5-dialog-action="save"
             class="button primary">OK</a>
          <a data-wysihtml5-dialog-action="cancel"
             class="button">Cancel</a>
          <a data-wysihtml5-dialog-action="searchImage"
             class="button primary">Search</a>
        </div>

      </div>
      <br/>

      <textarea id="textarea" name="pageContent"
                placeholder="Enter text ...">
        <xsl:value-of select="/data/page/content" />
      </textarea>
<br/>
<br/>
      <input type="hidden"
             name="path"
             value="{/data/system/baseUrl}/{requestUri}"/>
      <input type="submit"
             name="action"
             value="save"
             class="button primary"/>
      <a href="{/data/system/baseUrl}{/data/page/path}"
         class="backLink button">Zurück</a>
      <input type="hidden"
             name="content"
             value=""/>
    </form>


    <script>
      initEditor()
    </script>
  </xsl:template>

  <xsl:template name="breadcrumb">

    <ul class="breadcrumbs2 small">
      <li>
        <a href="{/data/system/baseUrl}">
          <span class="icon mif-home"></span>
        </a>
      </li>
      <xsl:for-each select="/data/system/breadcrumb/item">
        <li>
          <a href="{path}">
            <xsl:value-of select="label"/>
          </a>
        </li>
      </xsl:for-each>
    </ul>

  </xsl:template>


  <xsl:template name="toc">
    <div class="treeview" data-role="treeview">
      <ul>
        <xsl:for-each select="/data/system/toc/item">
          <xsl:call-template name="list-item">
            <xsl:with-param name="style" select="'node'"/>
            <xsl:with-param name="label" select="@name"/>
            <xsl:with-param name="link" select="@name"/>
          </xsl:call-template>
        </xsl:for-each>
      </ul>
    </div>
  </xsl:template>


  <xsl:template name="list-item">
    <xsl:param name="label"/>
    <xsl:param name="link"/>
    <xsl:param name="style"/>

    <xsl:choose>
      <xsl:when test="item">
        <li class="node">
          <xsl:choose>
            <xsl:when test="/data/system/requestUri/text() != ''">
              <xsl:call-template name="link">
                <xsl:with-param name="link">
                  <xsl:value-of select="/data/system/requestUri"/>
                  <xsl:value-of select="$link"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="$label"/>
              </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="link">
                <xsl:with-param name="link">
                  <xsl:value-of select="$link"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="$label"/>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>

          <span class="node-toggle"></span>
          <ul>
            <xsl:for-each select="item">
              <xsl:call-template name="list-item">
                <xsl:with-param name="link">
                  <xsl:value-of select="$link"/>.<xsl:value-of select="@name"/>
                </xsl:with-param>
                <xsl:with-param name="label" select="@name"/>
                <xsl:with-param name="style" select="''"/>
              </xsl:call-template>
            </xsl:for-each>
          </ul>
        </li>
      </xsl:when>
      <xsl:otherwise>
        <li class="{$style}">
          <span class="leaf">
            <xsl:call-template name="link">
              <xsl:with-param name="link" select="$link"/>
              <xsl:with-param name="label" select="$label"/>
            </xsl:call-template>
          </span>
        </li>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="link">
    <xsl:param name="label"/>
    <xsl:param name="link"/>

    <a class="leaf">
      <xsl:choose>
        <xsl:when test="/data/system/requestUri/text() != ''">
          <xsl:attribute name="href">
            <xsl:value-of select="/data/system/requestUri"/>.<xsl:value-of select="$link"/>
          </xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <xsl:attribute name="href">
            <xsl:value-of select="$link"/>
          </xsl:attribute>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:value-of select="$label"/>
    </a>
  </xsl:template>


  <xsl:template name="application-bar">
    <div class="app-bar">
      <ul class="app-bar-menu">
        <xsl:for-each select="/data/menu/item">
          <xsl:choose>
            <xsl:when test="item">
              <li>
                <a href="" class="dropdown-toggle">
                  <xsl:value-of select="label"/>
                </a>
                <ul class="d-menu" data-role="dropdown">
                  <xsl:for-each select="item">
                    <xsl:call-template name="application-bar-item">
                      <xsl:with-param name="label" select="label"/>
                      <xsl:with-param name="path" select="path"/>
                    </xsl:call-template>
                  </xsl:for-each>
                </ul>
              </li>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="application-bar-item">
                <xsl:with-param name="label" select="label"/>
                <xsl:with-param name="path" select="path"/>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>

        </xsl:for-each>
      </ul>
    </div>

  </xsl:template>

  <xsl:template name="application-bar-item">
    <xsl:param name="label"/>
    <xsl:param name="path"/>
    <li>
      <a href="{$path}">
        <xsl:value-of select="$label"/>
      </a>
    </li>
  </xsl:template>


</xsl:stylesheet>