@extends("admin.master.master")
@section("content")
    <div class="content-body">
        <!-- Breadcrumb-->
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <div class="breadcrumb-wrapper col-12">
                    <h2 class="content-header-title mb-0"><i class="la la-user-plus font-large-1"></i> Novo Cadastro
                    </h2>

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Clientes</a>
                        </li>
                        <li class="breadcrumb-item active">Cadastrar novo cliente</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Breadcrumb Fim-->

        <!-- Client Data -->
        <div class="row">
            <div class="col-12">
                @if($errors->all())
                    @foreach($errors->all() as $error)
                        @message(['type' => 'danger', 'icon' => 'la la-thumbs-down'])
                        <strong>Oops!</strong> {{ $error }}
                        @endmessage
                    @endforeach
                @endif

                @if(session()->exists('message'))
                    @message(['type' => session()->get('color'), 'icon' => session()->get('icon')])
                    {{ session()->get('message') }}
                    @endmessage
                @endif
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body p-0">
                            <ul class="nav nav-tabs  no-hover-bg nav-active-bordered-pill nav-justified nav-topline">
                                <li class="nav-item">
                                    <a class="nav-link active" id="active-tab1" data-toggle="tab" href="#active1"
                                       aria-controls="active1" aria-expanded="true">Dados Cadastrais</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="link-tab1" data-toggle="tab" href="#dataComplement"
                                       aria-controls="dataComplement" aria-expanded="false">Dados Complementares</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="linkOpt-tab1" data-toggle="tab" href="#admin"
                                       aria-controls="linkOpt1">Administrativo</a>
                                </li>
                            </ul>
                            <form action="{{ route('admin.users.store') }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="tab-content px-1 pt-1 ">
                                    <!-- Dados Cadastrais -->
                                    <div role="tabpanel" class="tab-pane active" id="active1"
                                         aria-labelledby="active-tab1" aria-expanded="true">
                                        <div class="card collapse-icon accordion-icon-rotate">

                                            <!-- Perfil -->
                                            <div class="label_gc">
                                                <span class="legend">Perfil:</span>
                                                <div class="d-inline-block custom-control custom-checkbox mr-1">
                                                    <input type="checkbox"
                                                           class="custom-control-input custom-control-input-green"
                                                           name="lessor"
                                                           {{ old('lessor') == 'on' || old('lessor') == true ? 'checked' : '' }} id="lessor">
                                                    <label class="custom-control-label custom-control-label-green text-white"
                                                           for="lessor">Locatário</label>
                                                </div>
                                                <div class="d-inline-block custom-control custom-checkbox mr-1">
                                                    <input type="checkbox"
                                                           class="custom-control-input custom-control-input-green"
                                                           name="lessee"
                                                           {{ old('lessee') == 'on' || old('lessee') == true ? 'checked' : '' }} id="lessee">
                                                    <label class="custom-control-label custom-control-label-green text-white"
                                                           for="lessee">Locador</label>
                                                </div>
                                            </div>
                                            <!-- Perfil Fim -->

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*Nome:</span>
                                                            <input class="form-control {{ ($errors->get('name') ? 'is-invalid' : '') }}"
                                                                   type="text" name="name"
                                                                   placeholder="Nome Completo"
                                                                   value="{{ old('name') }}"/>
                                                            @if($errors->get('name'))
                                                                <span class="invalid-feedback">{{ $errors->get('name')[0] }}</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*Genero:</span>
                                                            <select class="form-control" name="genre">
                                                                <option value="male" {{ old('genre') == 'male' ? 'selected' : '' }}>
                                                                    Masculino
                                                                </option>
                                                                <option value="female" {{ old('genre') == 'female' ? 'selected' : '' }}>
                                                                    Feminino
                                                                </option>
                                                                <option value="other" {{ old('genre') == 'other' ? 'selected' : '' }}>
                                                                    Outros
                                                                </option>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*CPF:</span>
                                                            <input type="text" class="mask-doc form-control"
                                                                   name="document"
                                                                   placeholder="CPF do Cliente"
                                                                   value="{{ old('document') }}"/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*RG:</span>
                                                            <input type="text" name="document_secondary"
                                                                   placeholder="RG do Cliente"
                                                                   value="{{ old('document_secondary') }}"
                                                                   class="form-control"/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">Órgão Expedidor:</span>
                                                            <input type="text"
                                                                   name="document_secondary_complement"
                                                                   placeholder="Expedição"
                                                                   value="{{ old('document_secondary_complement') }}"
                                                                   class="form-control"/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*Data de Nascimento:</span>
                                                            <input type="tel" name="date_of_birth"
                                                                   class="mask-date form-control"
                                                                   placeholder="Data de Nascimento"
                                                                   value="{{ old('date_of_birth') }}"/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*Naturalidade:</span>
                                                            <input type="text" name="place_of_birth"
                                                                   placeholder="Cidade de Nascimento"
                                                                   value="{{ old('place_of_birth') }}"
                                                                   class="form-control"/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">*Estado Civil:</span>
                                                            <select name="civil_status" class="form-control">
                                                                <optgroup label="Cônjuge Obrigatório">
                                                                    <option value="married" {{ old('civil_status') == 'married' ? 'selected' : '' }}>
                                                                        Casado
                                                                    </option>
                                                                    <option value="separated" {{ old('civil_status') == 'separated' ? 'selected' : '' }}>
                                                                        Separado
                                                                    </option>
                                                                </optgroup>
                                                                <optgroup label="Cônjuge não Obrigatório">
                                                                    <option value="single" {{ old('civil_status') == 'single' ? 'selected' : '' }}>
                                                                        Solteiro
                                                                    </option>
                                                                    <option value="divorced" {{ old('civil_status') == 'divorced' ? 'selected' : '' }}>
                                                                        Divorciado
                                                                    </option>
                                                                    <option value="widower" {{ old('civil_status') == 'widower' ? 'selected' : '' }}>
                                                                        Viúvo
                                                                    </option>
                                                                </optgroup>
                                                            </select>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label w-100">
                                                            <span class="legend">Foto</span>
                                                            <input type="file" name="cover" class="form-control">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Renda -->
                                            <div id="headingCollapse11"
                                                 class="card-header p-0 mb-1 border-bottom pb-1 ">
                                                <a data-toggle="collapse" href="#collapse11"
                                                   aria-expanded="false"
                                                   aria-controls="collapse11"
                                                   class="card-title lead primary p-0 font-weight-bold font-large-1">Renda</a>
                                            </div>
                                            <div id="collapse11" role="tabpanel"
                                                 aria-labelledby="headingCollapse11"
                                                 class="collapse" style="">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Profissão:</span>
                                                                <input type="text" name="occupation"
                                                                       placeholder="Profissão do Cliente"
                                                                       value="{{ old('occupation') }}"
                                                                       class="form-control"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Renda:</span>
                                                                <input type="tel" name="income"
                                                                       class="mask-money form-control"
                                                                       placeholder="Valores em Reais"
                                                                       value="{{ old('income') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Empresa:</span>
                                                                <input type="text" name="company_work"
                                                                       placeholder="Contratante"
                                                                       value="{{ old('company_work') }}"
                                                                       class="form-control"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- Renda Fim -->

                                            <!-- Endereço -->
                                            <div id="headingCollapse12"
                                                 class="card-header p-0 mb-1 border-bottom pb-1">
                                                <a data-toggle="collapse" href="#collapse12"
                                                   aria-expanded="false"
                                                   aria-controls="collapse12"
                                                   class="card-title lead primary p-0 font-weight-bold font-large-1">Endereço</a>
                                            </div>
                                            <div id="collapse12" role="tabpanel"
                                                 aria-labelledby="headingCollapse12"
                                                 class="collapse" aria-expanded="false" style="">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*CEP:</span>
                                                                <input type="tel" name="zipcode"
                                                                       class="mask-zipcode zip_code_search form-control"
                                                                       placeholder="Digite o CEP"
                                                                       value="{{ old('zipcode') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Endereço:</span>
                                                                <input type="text" name="street"
                                                                       class="street form-control"
                                                                       placeholder="Endereço Completo"
                                                                       value="{{ old('street') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Número:</span>
                                                                <input type="text" name="number"
                                                                       placeholder="Número do Endereço"
                                                                       value="{{ old('number') }}"
                                                                       class="form-control"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">Complemento:</span>
                                                                <input type="text" name="complement"
                                                                       placeholder="Completo (Opcional)"
                                                                       value="{{ old('complement') }}"
                                                                       class="form-control"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Bairro:</span>
                                                                <input type="text" name="neighborhood"
                                                                       class="form-control neighborhood"
                                                                       placeholder="Bairro"
                                                                       value="{{ old('neighborhood') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Estado:</span>
                                                                <input type="text" name="state"
                                                                       class="state form-control"
                                                                       placeholder="Estado"
                                                                       value="{{ old('state') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">*Cidade:</span>
                                                                <input type="text" name="city"
                                                                       class="city form-control"
                                                                       placeholder="Cidade"
                                                                       value="{{ old('city') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- Endereço Fim -->

                                            <!-- Contato -->
                                            <div id="headingCollapse13"
                                                 class="card-header p-0 mb-1 border-bottom pb-1">
                                                <a data-toggle="collapse" href="#collapse13"
                                                   aria-expanded="false"
                                                   aria-controls="collapse13"
                                                   class="card-title lead primary p-0 font-weight-bold font-large-1">Contato</a>
                                            </div>
                                            <div id="collapse13" role="tabpanel"
                                                 aria-labelledby="headingCollapse13"
                                                 class="collapse" aria-expanded="false">

                                                <div class="form-group">
                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <label class="label w-100">
                                                                <span class="legend">Residencial:</span>
                                                                <input type="tel" name="telephone"
                                                                       class="mask-phone form-control"
                                                                       placeholder="Número do Telefonce com DDD"
                                                                       value="{{ old('telephone') }}"/>
                                                            </label>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="label w-100">
                                                                <span class="legend">*Celular:</span>
                                                                <input type="tel" name="cell"
                                                                       class="mask-cell form-control"
                                                                       placeholder="Número do Telefonce com DDD"
                                                                       value="{{ old('cell') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- Contato Fim -->

                                            <!-- Acesso -->
                                            <div id="headingCollapse14"
                                                 class="card-header p-0 mb-1 border-bottom pb-1">
                                                <a data-toggle="collapse" href="#collapse14"
                                                   aria-expanded="false"
                                                   aria-controls="collapse14"
                                                   class="card-title lead primary p-0 font-weight-bold font-large-1">Acesso</a>
                                            </div>
                                            <div id="collapse14" role="tabpanel"
                                                 aria-labelledby="headingCollapse14"
                                                 class="collapse" aria-expanded="false" style="height: 0px;">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="label w-100">
                                                                <span class="legend">*E-mail:</span>
                                                                <input type="email" name="email"
                                                                       placeholder="Melhor e-mail"
                                                                       value="{{ old('email') }}" class="form-control"/>
                                                            </label>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Acesso Fim -->

                                        </div>
                                    </div>

                                    <!-- Dados Complementares -->
                                    <div class="tab-pane" id="dataComplement" role="tabpanel"
                                         aria-labelledby="link-tab1"
                                         aria-expanded="false">

                                        <!-- Cônjuge -->
                                        <div id="headingCollapse15"
                                             class="card-header p-0 mb-1 border-bottom pb-1">
                                            <a data-toggle="collapse" href="#collapse15"
                                               aria-expanded="false"
                                               aria-controls="collapse15"
                                               class="card-title lead primary p-0 font-weight-bold font-large-1">Cônjuge</a>
                                        </div>
                                        <div id="collapse15" role="tabpanel"
                                             aria-labelledby="headingCollapse15"
                                             class="collapse show" aria-expanded="false">

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">Tipo de Comunhão:</span>
                                                                <select name="type_of_communion"
                                                                        class="select2 form-control">
                                                                    <option value="Comunhão Universal de Bens" {{ old('type_of_communion') == 'Comunhão Universal de Bens' ? 'selected' : '' }}>
                                                                        Comunhão Universal de Bens
                                                                    </option>
                                                                    <option value="Comunhão Parcial de Bens" {{ old('type_of_communion') == 'Comunhão Parcial de Bens' ? 'selected' : '' }}>
                                                                        Comunhão Parcial de Bens
                                                                    </option>
                                                                    <option value="Separação Total de Bens" {{ old('type_of_communion') == 'Separação Total de Bens' ? 'selected' : '' }}>
                                                                        Separação Total de Bens
                                                                    </option>
                                                                    <option value="Participação Final de Aquestos" {{ old('type_of_communion') == 'Participação Final de Aquestos' ? 'selected' : '' }}>
                                                                        Participação Final de
                                                                        Aquestos
                                                                    </option>
                                                                </select>
                                                            </label>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">Nome:</span>
                                                                <input type="text" name="spouse_name"
                                                                       placeholder="Nome do Cônjuge"
                                                                       value="{{ old('spouse_name') }}"
                                                                       class="form-control"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="label w-100">
                                                            <span class="legend">Genero:</span>
                                                            <select name="spouse_genre" class="form-control">
                                                                <option value="male" {{ old('spouse_genre') == 'male' ? 'selected' : '' }}>
                                                                    Masculino
                                                                </option>
                                                                <option value="female" {{ old('spouse_genre') == 'female' ? 'selected' : '' }}>
                                                                    Feminino
                                                                </option>
                                                                <option value="other" {{ old('spouse_genre') == 'other' ? 'selected' : '' }}>
                                                                    Outros
                                                                </option>
                                                            </select>
                                                        </label>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">CPF:</span>
                                                                <input type="text" class="mask-doc form-control"
                                                                       name="spouse_document"
                                                                       placeholder="CPF do Cliente"
                                                                       value="{{ old('spouse_document') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">RG:</span>
                                                                <input type="text" class="form-control"
                                                                       name="spouse_document_secondary"
                                                                       placeholder="RG do Cliente"
                                                                       value="{{ old('spouse_document_secondary') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="label w-100">
                                                                <span class="legend">Órgão Expedidor:</span>
                                                                <input type="text" class="form-control"
                                                                       name="spouse_document_secondary_complement"
                                                                       placeholder="Expedição"
                                                                       value="{{ old('spouse_document_secondary_complement') }}"/>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="label w-100">
                                                            <span class="legend">Data de Nascimento:</span>
                                                            <input type="tel" class="mask-date form-control"
                                                                   name="spouse_date_of_birth"
                                                                   placeholder="Data de Nascimento"
                                                                   value="{{ old('spouse_date_of_birth') }}"/>
                                                        </label>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="label w-100">
                                                            <span class="legend">Naturalidade:</span>
                                                            <input type="text" name="spouse_place_of_birth"
                                                                   class="form-control"
                                                                   placeholder="Cidade de Nascimento"
                                                                   value="{{ old('spouse_occupation') }}"/>
                                                        </label>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="label w-100">
                                                            <span class="legend">Profissão:</span>
                                                            <input type="text" name="spouse_occupation"
                                                                   class="form-control"
                                                                   placeholder="Profissão do Cliente"
                                                                   value="{{ old('spouse_occupation') }}"/>
                                                        </label>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="label w-100">
                                                            <span class="legend">Renda:</span>
                                                            <input type="text" class="mask-money form-control"
                                                                   name="spouse_income"
                                                                   placeholder="Valores em Reais"
                                                                   value="{{ old('spouse_income') }}"/>
                                                        </label>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="label w-100">
                                                            <span class="legend">Empresa:</span>
                                                            <input type="text" class="form-control"
                                                                   name="spouse_company_work"
                                                                   placeholder="Contratante"
                                                                   value="{{ old('spouse_company_work') }}"/>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Cônjuge Fim -->

                                    </div>

                                    <!-- Administrativo -->
                                    <div class="tab-pane" id="admin" role="tabpanel"
                                         aria-labelledby="linkOpt-tab1" aria-expanded="false">

                                        <div class="label_gc">
                                            <span class="legend">Conceder:</span>

                                            <div class="d-inline-block custom-control custom-checkbox mr-1">
                                                <input type="checkbox"
                                                       class="custom-control-input custom-control-input-green"
                                                       name="admin"
                                                       id="adminProfile" {{ old('admin') == 'on' || old('admin') == true ? 'checked' : '' }}>
                                                <label class="custom-control-label custom-control-label-green text-white"
                                                       for="adminProfile">Administrativo</label>
                                            </div>

                                            <div class="d-inline-block custom-control custom-checkbox mr-1">
                                                <input type="checkbox"
                                                       class="custom-control-input custom-control-input-green"
                                                       name="client"
                                                       id="clientProfile" {{ old('client') == 'on' || old('client') == true ? 'checked' : '' }}>
                                                <label class="custom-control-label custom-control-label-green text-white"
                                                       for="clientProfile">Cliente</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-actions text-right">
                                            <button type="submit" class="btn mr-1 mb-1 btn-success"><i
                                                        class="la la-check-square"></i>
                                                Salvar Cliente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Client Data -->
    </div>
@endsection
@push("js")
    <script>
        // DATATABLES
        $('#dataTable').DataTable({
            responsive: true,
            "pageLength": 25,
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
        });
    </script>
@endpush