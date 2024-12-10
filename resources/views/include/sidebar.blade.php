   <!-- Side Menu -->
   <div class="sidemenu-area sidemenu-toggle default">
       <nav class="sidemenu navbar navbar-expand navbar-light hide-nav-title">
           <div class="navbar-collapse collapse">
               <div class="navbar-nav">
                   <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                       <i data-feather="grid" class="icon"></i>
                       <span class="title">
                           Dashboard
                       </span>
                   </a>

                   {{-- <a class="nav-link {{ Request::routeIs('complaint') ? 'active' : '' }}"
                       href="{{ route('complaint') }}">
                       <i data-feather="inbox" class="icon"></i>
                       <span class="title">Complaint List</span>
                   </a> --}}

                   <a class="nav-link {{ Request::routeIs('register.complaint') ? 'active' : '' }}"
                       href="{{ route('register.complaint') }}">
                       <i data-feather="check-square" class="icon"></i>
                       <span class="title">Complaint Register</span>
                   </a>

               </div>
           </div>
       </nav>
   </div>
   <!-- End Side Menu -->
