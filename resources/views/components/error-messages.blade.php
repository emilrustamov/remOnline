 <!--Start.Вывод ошибок -->
 @if (session()->has('error'))
     <div class="bg-red-500 text-white p-2 rounded mb-4">
         {{ session('error') }}
     </div>
 @endif

 @if ($errors->any())
     <div class="bg-red-500 text-white p-2 rounded mb-4">
         <ul>
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
 <!--End.Вывод ошибок -->
