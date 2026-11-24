<?php
  $menus = [
    'dashboard',
    'master' => [
        'category',
        'product',
        'inventory'
    ],
    'orders',
    'customers',
    'coupons',
    'reports'
];
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="">
        <div class="sidebar-brand" style="height: 65px;">
          <a href="{{ url('/dashboard') }}" class="brand-link">
            <img
              src="{{ asset('dist/assets/img/logo.png') }}"
              alt="AdminLTE Logo"
              class="" style="width: 67%;margin-left: 30px;"
            />

          </a>
          
        </div>
        
        
        
        <div class="sidebar-wrapper">
          <nav class="mt-2" aria-label="Main navigation">
            
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              data-accordion="false"
              id="navigation"
            >

              @foreach($menus as $key => $item)
                @php($isOpen = is_array($item) && collect($item)->contains(fn ($subitem) => request()->is($subitem, $subitem . '/*')))
                <li class="nav-item {{ $isOpen ? 'menu-open' : '' }}">
                  @if(is_array($item))
                    <a href="#" class="nav-link {{ $isOpen ? 'active' : '' }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                      <i class="nav-icon bi bi-box-seam-fill"></i>
                      <p>
                        {{ ucfirst($key) }}
                        <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @foreach($item as $subitem)
                        <li class="nav-item">
                          <a href="{{ url('/' . $subitem) }}" class="nav-link {{ request()->is($subitem, $subitem . '/*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-file-earmark"></i>
                            <p>{{ ucfirst($subitem) }}</p>
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <a href="{{ url('/' . $item) }}" class="nav-link {{ request()->is($item) ? 'active' : '' }}">
                      <i class="nav-icon bi bi-file-earmark"></i>
                      <p>{{ ucfirst($item) }}</p>
                    </a>
                  @endif
                </li>
              @endforeach
            </ul>
            

          </nav>
        </div>
        
      </aside>
