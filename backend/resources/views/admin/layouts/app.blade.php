<!DOCTYPE html>
<html lang="en">
    @include('admin.layouts.header')
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('admin.partials.navbar')
      @include('admin.partials.sidebar')
      <!--end::Sidebar-->
      <!--begin::App Main-->
        @yield('content')
      <!--end::App Main-->
      @include('admin.layouts.footer')
    </div>
    <!--end::App Wrapper-->
    </body>
</html>