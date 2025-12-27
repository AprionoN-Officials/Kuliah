        <div id="sidebar" class="sidebar sidebar-fixed expandable sidebar-light">
          <div class="sidebar-inner">

            <div class="ace-scroll flex-grow-1" data-ace-scroll="{}">

              <div class="sidebar-section my-2">
                <!-- the shortcut buttons -->
                <div class="sidebar-section-item fadeable-left">
                  <div class="fadeinable sidebar-shortcuts-mini">
                    <!-- show this small buttons when collapsed -->
                    <span class="btn btn-success p-0 opacity-1"></span>
                    <span class="btn btn-info p-0 opacity-1"></span>
                    <span class="btn btn-orange p-0 opacity-1"></span>
                    <span class="btn btn-danger p-0 opacity-1"></span>
                  </div>

                  <div class="fadeable">
                    <!-- show this small buttons when not collapsed -->
                    <div class="sub-arrow"></div>
                    <div>
                      <button class="btn px-25 py-2 text-95 btn-success opacity-1">
                        <i class="fa fa-signal f-n-hover"></i>
                      </button>

                      <button class="btn px-25 py-2 text-95 btn-info opacity-1">
                        <i class="fa fa-edit f-n-hover"></i>
                      </button>

                      <button class="btn px-25 py-2 text-95 btn-orange opacity-1">
                        <i class="fa fa-users f-n-hover"></i>
                      </button>

                      <button class="btn px-25 py-2 text-95 btn-danger opacity-1">
                        <i class="fa fa-cogs f-n-hover"></i>
                      </button>
                    </div>
                  </div>
                </div>


                <!-- the search box -->
                <div class="sidebar-section-item">
                  <i class="fadeinable fa fa-search text-info-m1 mr-n1"></i>

                  <div class="fadeable d-inline-flex align-items-center ml-3 ml-lg-0">
                    <i class="fa fa-search mr-n3 text-info-m1"></i>
                    <input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" placeholder="Search ..." aria-label="Search" />
                    <a href="#" class="ml-n25 px-2 py-1 radius-round bgc-h-secondary-l2 mb-2px">
                      <i class="fa fa-microphone px-3px text-dark-tp5"></i>
                    </a>
                  </div>
                </div>
              </div>

              <ul class="nav has-active-border active-on-right">


                <li class="nav-item-caption">
                  <span class="fadeable pl-3">MAIN</span>
                  <span class="fadeinable mt-n2 text-125">&hellip;</span>
                  <!--
               			OR something like the following (with `.hideable` text)
               		-->
                  <!--
               			<div class="hideable">
               				<span class="pl-3">MAIN</span>
               			</div>
               			<span class="fadeinable mt-n2 text-125">&hellip;</span>
               		-->
                </li>


                <li class="nav-item active">

                  <a href="dashboard.html" class="nav-link">
                    <i class="nav-icon fa fa-tachometer-alt"></i>
                    <span class="nav-text fadeable">
               	  <span>Dashboard</span>
                    </span>


                  </a>

                  <b class="sub-arrow"></b>

                </li>


                <li class="nav-item">

                  <a href="#" class="nav-link dropdown-toggle collapsed">
                    <i class="nav-icon fa fa-cube"></i>
                    <span class="nav-text fadeable">
               	  <span>Master</span>
                    </span>

                    <b class="caret fa fa-angle-left rt-n90"></b>

                    <!-- or you can use custom icons. first add `d-style` to 'A' -->
                    <!--
               	 	<b class="caret d-n-collapsed fa fa-minus text-80"></b>
               	 	<b class="caret d-collapsed fa fa-plus text-80"></b>
               	 -->
                  </a>

                  <div class="hideable submenu collapse">
                    <ul class="submenu-inner">

                      <li class="nav-item">

                        <a href="student.php" class="nav-link">

                          <span class="nav-text">
               				  <span>Student</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="course.php" class="nav-link">

                          <span class="nav-text">
               				  <span>Course</span>
                          </span>


                        </a>


                      </li>



                    </ul>
                  </div>

                  <b class="sub-arrow"></b>

                </li>


                <li class="nav-item">

                  <a href="#" class="nav-link dropdown-toggle collapsed">
                    <i class="nav-icon fa fa-desktop"></i>
                    <span class="nav-text fadeable">
               	  <span>Transaction</span>
                    </span>

                    <b class="caret fa fa-angle-left rt-n90"></b>

                    <!-- or you can use custom icons. first add `d-style` to 'A' -->
                    <!--
               	 	<b class="caret d-n-collapsed fa fa-minus text-80"></b>
               	 	<b class="caret d-collapsed fa fa-plus text-80"></b>
               	 -->
                  </a>

                  <div class="hideable submenu collapse">
                    <ul class="submenu-inner">

                      <li class="nav-item">

                        <a href="studentCourse.php" class="nav-link">

                          <span class="nav-text">
               				  <span>Student-Course</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="button-groups.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Button Groups</span>
                          </span>


                        </a>


                      </li>


                    </ul>
                  </div>

                  <b class="sub-arrow"></b>

                </li>



                <li class="nav-item-caption">
                  <span class="fadeable pl-3">OTHER</span>
                  <span class="fadeinable mt-n2 text-125">&hellip;</span>
                  <!--
               			OR something like the following (with `.hideable` text)
               		-->
                  <!--
               			<div class="hideable">
               				<span class="pl-3">OTHER</span>
               			</div>
               			<span class="fadeinable mt-n2 text-125">&hellip;</span>
               		-->
                </li>


                <li class="nav-item">

                  <a href="#" class="nav-link dropdown-toggle collapsed">
                    <i class="nav-icon fa fa-tag"></i>
                    <span class="nav-text fadeable">
               	  <span>More Pages</span>
                    <span class="badge badge-primary py-1 radius-round text-90 mr-2px badge-sm ">5</span>
                    </span>

                    <b class="caret fa fa-angle-left rt-n90"></b>

                    <!-- or you can use custom icons. first add `d-style` to 'A' -->
                    <!--
               	 	<b class="caret d-n-collapsed fa fa-minus text-80"></b>
               	 	<b class="caret d-collapsed fa fa-plus text-80"></b>
               	 -->
                  </a>

                  <div class="hideable submenu collapse">
                    <ul class="submenu-inner">

                      <li class="nav-item">

                        <a href="page-profile.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Profile</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-login.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Login</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-pricing.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Pricing</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-invoice.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Invoice</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-inbox.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Inbox</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-search.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Search Results</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="page-error.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Error</span>
                          </span>


                        </a>


                      </li>


                      <li class="nav-item">

                        <a href="starter.html" class="nav-link">

                          <span class="nav-text">
               				  <span>Starter</span>
                          </span>


                        </a>


                      </li>

                    </ul>
                  </div>

                  <b class="sub-arrow"></b>

                </li>

              </ul>

            </div><!-- /.sidebar scroll -->


            <div class="sidebar-section">
              <div class="sidebar-section-item fadeable-bottom">
                <div class="fadeinable">
                  <!-- shows this when collapsed -->
                  <div class="pos-rel">
                    <img alt="Alexa's Photo" src="assets/image/avatar/avatar3.jpg" width="42" class="px-1px radius-round mx-2 border-2 brc-default-m2" />
                    <span class="bgc-success radius-round border-2 brc-white p-1 position-tr mr-1 mt-2px"></span>
                  </div>
                </div>

                <div class="fadeable hideable w-100 bg-transparent shadow-none border-0">
                  <!-- shows this when full-width -->
                  <div id="sidebar-footer-bg" class="d-flex align-items-center bgc-white shadow-sm mx-2 mt-2px py-2 radius-t-1 border-x-1 border-t-2 brc-primary-m3">
                    <div class="d-flex mr-auto py-1">
                      <div class="pos-rel">
                        <img alt="Alexa's Photo" src="assets/image/avatar/avatar3.jpg" width="42" class="px-1px radius-round mx-2 border-2 brc-default-m2" />
                        <span class="bgc-success radius-round border-2 brc-white p-1 position-tr mr-1 mt-2px"></span>
                      </div>

                      <div>
                        <span class="text-blue-d1 font-bolder">Alexa</span>
                        <div class="text-80 text-grey">
                          Admin
                        </div>
                      </div>
                    </div>

                    <a href="#" class="d-style btn btn-outline-primary btn-h-light-primary btn-a-light-primary border-0 p-2 mr-2px ml-4" title="Settings" data-toggle="modal" data-target="#id-ace-settings-modal">
                      <i class="fa fa-cog text-150 text-blue-d2 f-n-hover"></i>
                    </a>

                    <a href="page-login.html" class="d-style btn btn-outline-orange btn-h-light-orange btn-a-light-orange border-0 p-2 mr-1" title="Logout">
                      <i class="fa fa-sign-out-alt text-150 text-orange-d2 f-n-hover"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>