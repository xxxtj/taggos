<?$c = new Controller(); 
?>
<div class="sidebar" data-background-color="white" data-active-color="danger">
      <div class="sidebar-wrapper">
            <div class="logo">
                <a href="/" class="simple-text">
                    TAGGOS.com
                </a>
            </div>

            <ul class="nav">
                <li>
                    <a href="/">
                        <i class="ti-home"></i>
                        <p>Website</p>
                    </a>
                </li>
                <li  class="<?=($c->url->controller == "main" && $c->url->method == "create_project_get") ? "active" : "" ?>">
                    <a href="/project">
                        <i class="ti-folder"></i>
                        <p>Create project</p>
                    </a>
                </li>
                <li class="<?=( ($c->url->controller == "main" && $c->url->method == "projects") || ($c->url->controller == "main" && $c->url->method == "report") ) ? "active" : "" ?>">
                    <a href="/projects">
                        <i class="ti-view-list-alt"></i>
                        <p>Projects</p>
                    </a>
                </li>
           <!--  <li class="active-pro">
                    <a href="upgrade.html">
                        <i class="ti-export"></i>
                        <p>Upgrade to PRO</p>
                    </a>
                </li> -->
            </ul>
      </div>
    </div>