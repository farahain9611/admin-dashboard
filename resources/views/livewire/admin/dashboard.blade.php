<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="container">
        <!-- <div align="right">
            <button class="btn btn-dark mt-2" onclick="document.body.classList.toggle('dark-mode')">Dark Mode</button>
        </div> -->
        <h2 class="text-xl font-bold">Admin Dashboard</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-white rounded shadow">
                <h3 class="text-lg">Total Users</h3>
                <p class="text-2xl font-bold">{{ $usersCount }}</p>
                <br>
                <a href="{{ route('admin.users') }}" class="btn btn-primary">Users</a>
            </div>
        </div>
    </div>
    <canvas id="usersChart"></canvas>
    @push('scripts')
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('usersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Users'],
                datasets: [{
                    label: 'Total Users',
                    data: [{{ $usersCount }}],
                    backgroundColor: 'blue'
                }]
            }
        });
    });
    </script>
    @endpush
</div>
