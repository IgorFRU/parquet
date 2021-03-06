@extends('layouts.admin-app')
@section('adminmenu')
    @parent
    @include('admin.partials.adminmenu')
@endsection
@section('content')
<div class="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <p class="h3">Товары @isset($parent_category)
                        из категории "{{ $parent_category }}"
                    @endisset
                    @isset($parent_manufacture)
                        производителя "{{ $parent_manufacture }}"
                    @endisset</p>
                    <div class="col-lg-2 d-flex">
                        <div>
                            <form action="{{ route('admin.products.copy') }}" method="post">
                                @csrf
                                <div class="hidden_inputs">
                                    <input type="hidden" name="product_group_ids[]">
                                </div>
                                <button type="submit" class="btn btn-link product_group_copy disabled mr-1 bg-success text-white" href="#"><i class="fas fa-copy"></i></button>
                            </form>
                        </div>
                        <form action="{{ route('admin.products.published') }}" method="post">
                            @csrf
                            <div class="hidden_inputs">
                                <input type="hidden" name="product_group_ids[]">
                            </div>
                            <button type="submit" class="btn product_group_published disabled mr-1 bg-success text-white" href="#"><i class="fas fa-eye"></i></button>
                        </form>
                        <button type="button" class="btn bg-warning product_group_delete disabled" disabled data-toggle="modal" data-target=".modalDeleteProduct"><i class="fas fa-trash-alt"></i></button>
                    </div>

                    <button class="btn btn-sm" data-toggle="modal" data-target="#productSearchModal"><i class="fas fa-search"></i> Поиск...</button>

                    <div class="row col-md-6">
                        <div class="col-md-5">
                            <select class="form-control" id="index_category_id" name="index_category_id">
                                <option value="0">-- Все категории --</option>
                                @include('admin.products.partials.categories', ['categories' => $categories, 'delimiter' => $delimiter])
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select class="form-control" id="index_manufacture_id" name="index_manufacture_id">
                                <option value="0">-- Все производители --</option>
                                
                                @foreach ($manufactures as $manufacture)
                                    <option value="{{ $manufacture->id }}"
                                        @isset($current_manufacture)
                                            @if($current_manufacture == $manufacture->id)
                                                selected="selected"
                                            @endif
                                            @if ($manufacture->products->count() == 0)
                                                disabled='disabled'
                                            @endif
                                        @endisset 
                                        @isset($product->category_id)
                                            @if ($category_list->id == $product->category_id)
                                            selected = "selected"
                                            @endif
                                        @endisset 
                                        >{{ $manufacture->manufacture }} ({{ $manufacture->products->count() }})</option>
                                                                     
                                @endforeach
                            </select>
                        </div> 
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary col-md-2">Новый товар</a> 
                    </div>
                                   
                </div>
                <div class="col-md-12">
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col"></th>
                            <th scope="col">Арт.</th>
                            <th scope="col">Товар</th>
                            <th scope="col">Цена</th>
                            <th scope="col">Категория</th>
                            <th scope="col">Наличие</th>
                            <th scope="col">Срок доставки</th>
                            <th scope="col">-</th>
                            <th scope="col">-</th>
                            {{-- <th scope="col">Описание</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $count = 1
                        @endphp   
                        @forelse ($products as $product)
                        @php
                            // dd($product)
                        @endphp
                        <tr @if (!$product->published) class='bg-secondary'  @endif>
                            <th scope="row">{{ $count++ }}</th>
                            <td>
                                <input class="form-check-input product_id"  data-toggle="tooltip" data-placement="top" title="id: {{ $product->id }}" type="checkbox" value="{{ $product->id }}" id="product_id_{{ $product->id }}">
                            </td>
                            <td>{{ $product->autoscu }}</td>
                            <td>{{ $product->product }}</td>
                            <td>
                                @if(isset($product->discount) && $product->actually_discount)
                                    @if ($product->discount->type == '%')
                                        <div class='btn-group' role="group">
                                            <div class="btn text-light bg-success btn-sm" data-toggle="tooltip" data-placement="top" title="Акция '{{ $product->discount->discount }}' до {{ $product->discount->d_m_y ?? '' }}"> 
                                                {{ $product->price * $product->discount->numeral }} {!! $product->currency->css_style ?? $product->currency->currency_rus !!}
                                            </div>
                                            <div class="btn text-light bg-secondary btn-sm">{{ $product->price_number }} {!! $product->currency->css_style ?? $product->currency->currency_rus !!}</div>
                                        </div>
                                    @elseif ($product->discount->type == 'rub')
                                        <div class='btn-group' role="group">
                                            <div class="btn text-light bg-success btn-sm" data-toggle="tooltip" data-placement="top" title="Акция '{{ $product->discount->discount }}' до {{ Carbon\Carbon::parse($product->discount->discount_end)->locale('ru')->isoFormat('DD MMMM YYYY', 'Do MMMM') }}">
                                                {{ $product->price - $product->discount->value }} {!! $product->currency->css_style ?? $product->currency->currency_rus !!}
                                            </div>
                                            <div class="btn text-light bg-secondary btn-sm">{{ $product->price_number }} {!! $product->currency->css_style ?? $product->currency->currency_rus !!}</div>
                                    @endif
                                @else
                                    <div class="btn text-light bg-success btn-sm">{{ $product->price_number }} {!! $product->currency->css_style ?? $product->currency->currency_rus !!}</div> 
                                @endif
                                
                            
                            
                            </td>
                            <td>{{ $product->category->category ?? '' }}</td>
                            {{-- <td>{{ $product->manufactures->manufacture }}</td> --}}
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->delivery_time }}</td>
                            <td>
                                <a class="btn btn-outline-info btn-sm" href="{{ route('admin.products.create', ['product' => $product->id]) }}" role="button"><i class="fas fa-code-branch"></i></a>                                
                            </td>
                            <td>
                                <div class='row'>                                
                                    <a href="{{ route('admin.products.edit', ['id' => $product->id]) }}" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></a>
                                    <form onsubmit="if(confirm('Удалить?')) {return true} else {return false}" action="{{route('admin.products.destroy', $product)}}" method="post">
                                    @csrf                         
                                    <input type="hidden" name="_method" value="delete">                         
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>                                                 
                                </form>
                                </div>
                            </td>
                        </tr>
                        
                        @empty
                            <div class="alert alert-warning">Вы еще не добавили ни одного товара!</div>
                        @endforelse
                    </tbody>
                </table>
                <div class="paginate">
                    {{ $products->appends(request()->input())->links('layouts.pagination') }}
                </div>
                <div class="items_per_page">
                    <form  class="form-group row col-lg-6" action="{{ route('admin.products.index') }}" method="get">
                        <label for="pp" class="col-lg-3 col-form-label">Товаров на странице</label>
                        <div class="col-lg-2">
                            @php
                                $perPage = 5;
                                $count = 5;
                            @endphp
                            <select class="form-control" name="pp" id="pp">
                                @for ($i = 1; $i < $count; $i++)
                                @php
                                    $pP = $perPage * pow(2, $i);
                                @endphp
                                    <option @if ($pP == $itemsPerPage) selected='selected' @endif value="{!! $pP !!}">{!! $pP !!}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                                <select class="form-control" id="p_published" name="p_published">
                                    <option @if ($productPublished == 2) selected='selected' @endif value="2">Все</option>
                                    <option @if ($productPublished == 1) selected='selected' @endif value="1">Опублик.</option>
                                    <option @if ($productPublished == 0) selected='selected' @endif value="0">Неопублик.</option>
                                </select>
                            </div>
                        <button class="btn button-primary" type="submit">OK</button>        
                    </form> 
                </div>                
            </div>
        </div>
    </div>
</div>
<div class="modal fade modalDeleteProduct" tabindex="-1" role="dialog" aria-labelledby="modalDeleteProduct" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Удаление товаров</h4>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить выбранные товары? Отменить выбранное действие будет невозможно!
            </div>
            <div class="modal-footer">
                <form action="{{ route('admin.products.massdestroy') }}" method="post">
                    @csrf
                    <div class="hidden_inputs">
                        <input type="hidden" name="product_group_ids[]">
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning">Да</button>
                </form>
            </div>
        </div>      
    </div>
</div>

<div class="modal fade" id="productSearchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="get">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Поиск товара (по внутреннему артикулу и артикулу производителя, названию, описанию, цене)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="input" class="form-control" id="productSearch" placeholder="Поиск...">
                    <div class="my-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Внутр. арт.</th>
                                <th scope="col">Арт.</th>
                                <th scope="col">Название</th>
                                <th scope="col">Цена (без скидки)</th>
                                <th scope="col">Категория</th>
                                <th scope="col">Наличие</th>
                                </tr>
                            </thead>
                            <tbody  id="productSearchResult">
                                <tr class="productSearchResult_item template hide">
                                    <th scope="row" class="productSearchResult_number"></th>
                                    <td class="autoscu"></td>
                                    <td class="scu"></td>
                                    <td class="product"><a href="" target="_blank"></a></td>
                                    <td class="price"></td>
                                    <td class="category"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="loader_animate hide">
                            <div class="cssload-loader">
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Все результаты поиска</button>
                </div>
            </form>
        </div>
    </div>
  </div>
@endsection