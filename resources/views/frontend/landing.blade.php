<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
<meta charset="UTF-8">
<title>منصة الرحلات</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-50">

<!-- Navbar -->

<nav class="bg-white shadow">

<div class="container mx-auto px-6 py-4 flex justify-between">

<h1 class="text-2xl font-bold text-blue-600">
منصة الرحلات
</h1>

<div>

@auth

<a href="/customer/dashboard" class="text-gray-700 mx-3">
لوحة التحكم
</a>

@else

<a href="{{ route('login') }}" class="mx-3 text-gray-700">
تسجيل الدخول
</a>

<a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
إنشاء حساب
</a>

@endauth

</div>

</div>

</nav>

<!-- Hero Section -->

<section class="bg-blue-600 text-white py-24">

<div class="container mx-auto text-center">

<h2 class="text-5xl font-bold mb-6">
اكتشف أجمل الرحلات حول العالم
</h2>

<p class="text-xl mb-8">
احجز رحلتك بسهولة مع أفضل الشركات السياحية
</p>

<a href="/trips" class="bg-white text-blue-600 px-6 py-3 rounded text-lg font-bold">
استكشف الرحلات
</a>

</div>

</section>

<!-- Search -->

<section class="py-16">

<div class="container mx-auto">

<div class="bg-white shadow-lg rounded-lg p-6">

<form class="grid grid-cols-4 gap-4">

<input
type="text"
placeholder="الوجهة"
class="border p-3 rounded"
/>

<input
type="date"
class="border p-3 rounded"
/>

<input
type="number"
placeholder="عدد الأشخاص"
class="border p-3 rounded"
/>

<button
class="bg-blue-600 text-white rounded p-3">
بحث
</button>

</form>

</div>

</div>

</section>

<!-- Featured Trips -->

<section class="py-20 bg-gray-100">

<div class="container mx-auto">

<h3 class="text-3xl font-bold text-center mb-12">
الرحلات المميزة
</h3>

<div class="grid grid-cols-3 gap-8">

@foreach($trips ?? [] as $trip)

<div class="bg-white rounded shadow">

<img
src="{{ $trip->image ?? 'https://picsum.photos/400/200' }}"
class="rounded-t"
/>

<div class="p-4">

<h4 class="text-xl font-bold">
{{ $trip->title }}
</h4>

<p class="text-gray-500">
{{ $trip->location }}
</p>

<p class="text-blue-600 font-bold mt-2">
$ {{ $trip->price }}
</p>

<a
href="/trips/{{ $trip->id }}"
class="block mt-4 bg-blue-600 text-white text-center py-2 rounded">

عرض الرحلة

</a>

</div>

</div>

@endforeach

</div>

</div>

</section>

<!-- Why us -->

<section class="py-20">

<div class="container mx-auto text-center">

<h3 class="text-3xl font-bold mb-12">
لماذا تختارنا؟
</h3>

<div class="grid grid-cols-3 gap-10">

<div>
<h4 class="text-xl font-bold mb-2">
أفضل الأسعار
</h4>
<p class="text-gray-500">
نقدم أفضل العروض السياحية
</p>
</div>

<div>
<h4 class="text-xl font-bold mb-2">
حجز سريع
</h4>
<p class="text-gray-500">
احجز رحلتك في دقائق
</p>
</div>

<div>
<h4 class="text-xl font-bold mb-2">
دعم 24/7
</h4>
<p class="text-gray-500">
فريق دعم متواجد دائماً
</p>
</div>

</div>

</div>

</section>

<!-- Footer -->

<footer class="bg-gray-900 text-white py-10">

<div class="container mx-auto text-center">

<p>
© 2026 منصة الرحلات - جميع الحقوق محفوظة
</p>

</div>

</footer>

</body>

</html>