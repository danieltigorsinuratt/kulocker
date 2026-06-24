<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KuLocker — Profil</title>
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
</head>
<body>
<main class="page-wrapper">
  <header class="navbar">
    <div class="brand-with-back">
      <a href="{{ route('dashboard') }}" class="brand-back">
        <i class="ti ti-arrow-left"></i> Kembali
      </a>
    </div>
    <div class="brand"><span class="brand-dot"></span>KuLocker</div>
    <div class="user-pill">Profil Saya</div>
  </header>

  @if ($flash)
    <div class="flash flash-{{ $flash['ok'] ? 'success' : 'error' }}">{{ $flash['msg'] }}</div>
  @endif

  <div class="hero-banner">
    <button id="btnEdit" class="btn-edit {{ ($stay_edit || $tab==='keamanan') ? 'd-none':'' }}" onclick="setMode(true)">
      <i class="ti ti-edit"></i> Edit Profil
    </button>
    <button id="btnCancel" class="btn-cancel {{ $stay_edit ? '':'d-none' }}" onclick="setMode(false)">
      <i class="ti ti-x"></i> Batal
    </button>
  </div>

  <section class="content-grid">
    <aside class="left-panel">
      <div class="avatar-wrap">
        <div class="avatar-circle">
          @if (!empty($profil['foto_profil']))
            <img id="avatarImg" src="{{ asset('image/'.$profil['foto_profil']) }}" onerror="this.style.display='none';document.getElementById('avatarIni').style.display='block'" />
          @else
            <img id="avatarImg" src="" style="display:none" onerror="this.style.display='none';document.getElementById('avatarIni').style.display='block'" />
          @endif
          @php
            $inisial = 'U';
            if (!empty($profil['nama'])) {
                $p = explode(' ', trim($profil['nama']));
                $inisial = strtoupper($p[0][0] . (count($p)>1 ? end($p)[0] : ''));
            }
          @endphp
          <span id="avatarIni" {{ !empty($profil['foto_profil']) ? 'style=display:none':'' }}>{{ $inisial }}</span>
        </div>
        <button class="avatar-edit-btn {{ $stay_edit ? '':'d-none' }}" id="avatarBtn" onclick="document.getElementById('inputFoto').click()" title="Ganti foto">
          <i class="ti ti-camera"></i>
        </button>
      </div>
      <h2>{{ $profil['nama'] ?? 'Pengguna' }}</h2>
      <p class="subtitle">Mahasiswa, UNRAM</p>
      <p class="avatar-preview-hint d-none" id="fotoHintSide">📷 Foto baru dipilih</p>
    </aside>

    <article>
      <div class="form-tabs">
        <div class="tab {{ $tab==='profil' ? 'active':'' }}" id="tabProfil" onclick="switchTab('profil')">Profil</div>
        <div class="tab {{ $tab==='keamanan' ? 'active':'' }}" id="tabKeamanan" onclick="switchTab('keamanan')">Keamanan</div>
      </div>

      <div id="panelProfil" class="{{ $tab!=='profil' ? 'd-none':'' }}">
        <div id="viewMode" class="editor-grid {{ $stay_edit ? 'd-none':'' }}">
          @foreach(['Nama Lengkap'=>'nama','NIM'=>'nim','Alamat'=>'alamat','Email'=>'email','No. HP'=>'no_hp'] as $lbl => $k)
            <div class="form-group {{ $k==='alamat'?'full':'' }}">
              <label>{{ $lbl }}</label>
              <p class="text-value">{{ $profil[$k] ?? '-' }}</p>
            </div>
          @endforeach
        </div>

        <form id="editMode" class="editor-grid is-form {{ $stay_edit ? '':'d-none' }}" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="aksi" value="profil" />
          <div class="form-group full">
            <label>Foto Profil</label>
            <input type="file" id="inputFoto" name="foto_baru" accept=".jpg,.jpeg,.png,.gif,.webp" style="display:none" onchange="previewFoto(this)" />
            <div style="display:flex;align-items:center;gap:10px">
              <button type="button" class="btn-secondary" style="padding:7px 14px;font-size:.82rem" onclick="document.getElementById('inputFoto').click()">
                <i class="ti ti-upload"></i> Pilih Foto
              </button>
              <span class="field-hint" id="fotoHint">JPG, PNG, GIF, WEBP — maks 2 MB</span>
            </div>
          </div>
          
          <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama" required value="{{ $profil['nama'] ?? '' }}" /></div>
          <div class="form-group"><label>NIM</label><input type="text" name="nim" value="{{ $profil['nim'] ?? '' }}" /></div>
          <div class="form-group full"><label>Alamat</label><textarea name="alamat">{{ $profil['alamat'] ?? '' }}</textarea></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ $profil['email'] ?? '' }}" /></div>
          <div class="form-group"><label>No. HP</label><input type="text" name="no_hp" value="{{ $profil['no_hp'] ?? '' }}" /></div>

          <div class="button-group">
            <button type="submit" class="btn-save"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
            <button type="button" class="btn-secondary" onclick="setMode(false)">Batal</button>
          </div>
        </form>
      </div>

      <div id="panelKeamanan" class="{{ $tab!=='keamanan' ? 'd-none':'' }}">
        <form class="editor-grid is-form" action="{{ route('profile.password') }}" method="POST" onsubmit="return validasiPwd()">
          @csrf
          <input type="hidden" name="aksi" value="password" />
          <div class="security-info full"><i class="ti ti-shield-lock"></i> Untuk mengubah kata sandi, mohon isi field di bawah ini.</div>

          @php 
          $pwdfields = [
              ['Lama', 'pwd-lama', 'current-password', "clearErr('err-lama')"],
              ['Baru', 'pwd-baru', 'new-password', "kekuatan(); cekKonfirm(); clearErr('err-baru')"],
              ['Konfirmasi', 'pwd-konfirm', 'new-password', "cekKonfirm(); clearErr('err-konfirm')"]
          ];
          @endphp
          @foreach($pwdfields as $f)
            <div class="form-group {{ $f[0]==='Lama'?'full':'' }}">
              <label>Password {{ $f[0] }}</label>
              <div class="pwd-group">
                <input type="password" name="{{ str_replace('-','_',$f[1]) }}" id="{{ $f[1] }}" autocomplete="{{ $f[2] }}" oninput="{!! $f[3] !!}" />
                <button type="button" class="pwd-toggle" onclick="togglePwd('{{ $f[1] }}',this)"><i class="ti ti-eye"></i></button>
              </div>
              <span class="field-error" id="err-{{ strtolower(explode('-',$f[1])[1]) }}"></span>
            </div>
          @endforeach

          <div class="form-group full" id="kekuatanWrap" style="display:none">
            <label>Kekuatan password</label>
            <div style="height:5px;border-radius:3px;background:#E5E7EB;overflow:hidden;margin-top:3px">
              <div id="kekuatanBar" style="height:100%;border-radius:3px;transition:all .3s;width:0"></div>
            </div>
            <span id="kekuatanLabel" class="field-hint" style="margin-top:3px"></span>
          </div>

          <div class="button-group">
            <button type="submit" class="btn-save"><i class="ti ti-lock"></i> Perbarui Password</button>
            <button type="button" class="btn-secondary" onclick="resetPwd()"><i class="ti ti-refresh"></i> Reset</button>
          </div>
        </form>
      </div>
    </article>
  </section>
</main>

<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>
