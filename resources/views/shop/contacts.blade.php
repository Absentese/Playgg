@extends('shop.layouts.app')
@section('title', 'Контакты')
@section('content')
<div class="page-header">
    <div class="container text-center">
        <h1 class="display-6 fw-bold mb-2 text-white"><i class="fas fa-envelope me-2"></i>Контакты</h1>
        <p class="lead mb-0 opacity-90">Вопросы по заказам, ключам и пополнению Steam</p>
    </div>
</div>
<section class="py-5 shop-page-section">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="content-card shop-info-card h-100">
                    <div class="shop-info-card__icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h2 class="h5 fw-bold text-white mb-2">Адрес</h2>
                    <p class="shop-info-card__text mb-0">г. Чебоксары, ТЦ «МегаМолл», 3 этаж</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="content-card shop-info-card h-100">
                    <div class="shop-info-card__icon"><i class="fas fa-phone"></i></div>
                    <h2 class="h5 fw-bold text-white mb-2">Связь</h2>
                    <p class="shop-info-card__text">
                        <a href="tel:+79991002030" class="shop-info-link">+7 (999) 100-20-30</a><br>
                        <a href="mailto:info@playgg.ru" class="shop-info-link">info@playgg.ru</a>
                    </p>
                    <a href="https://max.ru" target="_blank" rel="noopener" class="shop-info-messenger">
                        <img src="{{ asset('images/icon-max.svg') }}" alt="" width="20" height="20">
                        Мессенджер MAX
                    </a>
                    <p class="shop-info-card__text mb-0 mt-3"><strong class="text-white">Часы работы:</strong> Пн–Вс 9:00–20:00</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card shop-form-card">
                    <h2 class="h4 fw-bold text-white text-center mb-4">Напишите нам</h2>
                    <form method="POST" action="{{ route('contacts.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Имя</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                       placeholder="Как к вам обращаться" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                       placeholder="email@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Телефон</label>
                                <input type="text" name="phone" class="form-control" data-phone-mask
                                       value="{{ old('phone') }}"
                                       placeholder="+7 (999) 123-45-67">
                                <div class="form-text">Необязательно — позвоним, если понадобится уточнение</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Сообщение</label>
                                <textarea name="message" class="form-control" rows="4" required
                                          placeholder="Вопрос по заказу, ключу, оплате или пополнению Steam">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 text-center pt-2">
                                <button type="submit" class="btn btn-glow rounded-pill px-5 py-2">Отправить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
