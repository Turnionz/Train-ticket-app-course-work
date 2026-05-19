<x-layout>
    <h1 class="my-16 text-center text-4xl font-medium text-slate-600">
        Створення нового робітника
    </h1>

    <x-card class="py-8 px-16">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="mb-8">
                <label for="email">E-mail</label>
                <x-search-bar type="text" name="email" />
            </div>

            <div class="mb-8">
                <label for="first_name">Ім'я</label>
                <x-search-bar type="text" name="first_name" />
            </div>

            <div class="mb-8">
                <label for="last_name">Прізвище</label>
                <x-search-bar type="text" name="last_name" />
            </div>

            <div class="mb-8 grid grid-rows-2 relative" id="custom-role-container">
                <label for="role">Роль працівника</label>
                
                <select name="role" id="role" class="hidden">
                    @if (auth()->user()->role === \App\Models\User::$role[0])
                        <option value="admin">Адмін</option>
                        <option value="operator">Оператор</option>
                    @endif
                    @if (in_array(auth()->user()->role, [\App\Models\User::$role[0], \App\Models\User::$role[1]]))
                        <option value="employee" selected>Працівник</option>
                    @endif
                </select>

                <div class="relative">
                    <button type="button" id="role-display" class="w-full text-left bg-white border border-gray-300 rounded-md p-2 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span>Виберіть роль...</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="role-menu" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            @if (auth()->user()->role === \App\Models\User::$role[0])
                                <li data-value="admin" class="custom-role-option px-3 py-2 cursor-pointer hover:bg-gray-200 w-full">Адмін</li>
                                <li data-value="operator" class="custom-role-option px-3 py-2 cursor-pointer hover:bg-gray-200 w-full">Оператор</li>
                            @endif
                            @if (in_array(auth()->user()->role, [\App\Models\User::$role[0], \App\Models\User::$role[1]]))
                                <li data-value="employee" class="custom-role-option px-3 py-2 cursor-pointer hover:bg-gray-200 w-full">Працівник</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-8 grid grid-rows-2 relative" id="custom-type-container">
                <label for="type">Позиція</label>
                
                <select name="type" id="type" class="hidden">
                    @foreach (\App\Models\Employee::$type as $typeOption)
                        <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <button type="button" id="type-display" class="w-full text-left bg-white border border-gray-300 rounded-md p-2 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        <span>Виберіть позицію...</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="type-menu" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            @foreach (\App\Models\Employee::$type as $typeOption)
                                <li data-value="{{ $typeOption }}" class="custom-type-option px-3 py-2 cursor-pointer hover:bg-gray-200 w-full">{{ $typeOption }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const roleSelect = document.getElementById('role');
                    const roleDisplayBtn = document.getElementById('role-display');
                    const roleDisplaySpan = roleDisplayBtn.querySelector('span');
                    const roleMenu = document.getElementById('role-menu');
                    const customRoleOptions = document.querySelectorAll('.custom-role-option');

                    const typeSelect = document.getElementById('type');
                    const typeDisplayBtn = document.getElementById('type-display');
                    const typeDisplaySpan = typeDisplayBtn.querySelector('span');
                    const typeMenu = document.getElementById('type-menu');
                    const customTypeOptions = document.querySelectorAll('.custom-type-option');

                    function toggleTypeDropdown() {
                        if (roleSelect.value !== 'employee') {
                            typeSelect.disabled = true;
                            typeSelect.selectedIndex = -1; 
                            
                            typeDisplaySpan.textContent = 'Виберіть позицію...'; 
                            typeDisplayBtn.disabled = true;
                            typeDisplayBtn.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                            typeDisplayBtn.classList.remove('bg-white');
                            typeMenu.classList.add('hidden'); 
                        } else {
                            typeSelect.disabled = false;
                            typeDisplayBtn.disabled = false;
                            typeDisplayBtn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                            typeDisplayBtn.classList.add('bg-white');
                        }
                    }

                    function setupCustomDropdown(selectEl, displayBtn, displaySpan, menuEl, optionsList, containerId) {
                        if (selectEl.options.length > 0 && selectEl.selectedIndex !== -1) {
                            displaySpan.textContent = selectEl.options[selectEl.selectedIndex].text;
                        }

                        displayBtn.addEventListener('click', function(e) {
                            e.stopPropagation(); 
                            if (!this.disabled) {
                                if (containerId === 'custom-role-container') typeMenu.classList.add('hidden');
                                if (containerId === 'custom-type-container') roleMenu.classList.add('hidden');
                                
                                menuEl.classList.toggle('hidden');
                            }
                        });

                        optionsList.forEach(option => {
                            option.addEventListener('click', function() {
                                selectEl.value = this.getAttribute('data-value');
                                
                                displaySpan.textContent = this.textContent;
                                menuEl.classList.add('hidden');

                                selectEl.dispatchEvent(new Event('change')); 
                            });
                        });


                        document.addEventListener('click', function(e) {
                            if (!document.getElementById(containerId).contains(e.target)) {
                                menuEl.classList.add('hidden');
                            }
                        });
                    }

                    setupCustomDropdown(roleSelect, roleDisplayBtn, roleDisplaySpan, roleMenu, customRoleOptions, 'custom-role-container');
                    setupCustomDropdown(typeSelect, typeDisplayBtn, typeDisplaySpan, typeMenu, customTypeOptions, 'custom-type-container');

                    toggleTypeDropdown();
                    roleSelect.addEventListener('change', toggleTypeDropdown);
                });
            </script>

            <div class="mb-8">
                <label for="password">Пароль</label>
                <x-search-bar type="password" name="password" />
            </div>

            <button class="w-full bg-green-500 text-2xl font-semibold rounded-md">Зареєструвати</button>
        </form>
    </x-card>
</x-layout>