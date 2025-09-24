<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="{{ asset('vendors/img/logo7.jpg') }}" alt="Site Logo" class="brand-image" style="opacity: 1">
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- User Panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      
      <div class="info">
        <a href="#" class="d-block">{{ Auth::user()->name ?? 'Admin' }}: CBT Admin</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <!-- SECTION: Dashboard -->
        <li class="nav-item">
          <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- SECTION: Exam Management -->
        <li class="nav-item has-treeview {{ request()->is('questionbank*') || request()->is('admin/exams/create') || request()->is('admin/questions/view') || request()->is('admin/exam-cards') || request()->is('admin/exam-access') || request()->is('admin/exams/create-question') || request()->is('question-bank/exams/*/edit') || request()->is('admin/exams/create') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->is('questionbank*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-question-circle"></i>
            <p>
              Exam Management
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.exams.create') }}" class="nav-link {{ request()->is('admin/exams/create') || request()->is('question-bank/exams/*/edit') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Create Exam</p>
              </a>
            </li>
            <li class="nav-item" >
              <a href="{{ route('admin.questionbank.create') }}" class="nav-link {{ request()->is('admin/exams/create-question') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Question</p>
              </a>
            </li>
            <li class="nav-item"> 
              <a href="{{ route('admin.questions.view') }}" class="nav-link {{ request()->is('admin/questions/view') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i> 
                <p>Questions</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.exam_cards.index') }}" class="nav-link {{ request()->is('admin/exam-cards') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Exam Cards</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.exam-access.index') }}" class="nav-link {{ request()->is('admin/exam-access') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Exam Access</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- SECTION: Results -->
        <li class="nav-item">
          <a href="{{ route('admin.result.index') }}" class="nav-link {{ request()->is('admin/results') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Results</p>
          </a>
        </li>

        <!-- SECTION: Student & Data Sync -->
        <li class="nav-item has-treeview {{ request()->is('cbt/sync*') || request()->is('admin/upload-student-index') || request()->is('admin/clear-session') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>
              Student Management
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('student-index.form') }}" class="nav-link {{ request()->is('admin/upload-student-index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Upload Student Index</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('sync.settings') }}" class="nav-link {{ request()->is('cbt/sync/settings') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Run Migration</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.clear.session.page') }}" class="nav-link {{ request()->is('admin/clear-session') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Clear Exam session</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('exams.index') }}" class="nav-link {{ request()->is('exams') || request()->is('exams/*/monitor') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Exams Monitoring</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.results.clear') }}" class="nav-link {{ request()->is('admin/results/clear') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Clear Results</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- SECTION: System Settings -->
        <li class="nav-item">
          <a href="{{ route('admin.backgrounds.index') }}" class="nav-link {{ request()->is('admin/backgrounds') ? 'active' : '' }}">
            <i class="nav-icon fas fa-image"></i>
            <p>Background Image</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>Profile Update</p>
          </a>
        </li>

        <!-- SECTION: Logout -->
        <li class="nav-item">
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-left text-white" style="width: 100%;">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </button>
          </form>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
