<div>
    <div
          x-data="{ 
              show: @entangle('notification.show'),
              message: @entangle('notification.message')
          }"
          x-show="show"
          x-init="$watch('show', value => {
              if (value) {
                  setTimeout(() => {
                      show = false;
                  }, 3000)
              }
          })"
          x-transition:enter="transform ease-out duration-300"
          x-transition:enter-start="translate-x-64 opacity-0"
          x-transition:enter-end="translate-x-0 opacity-100"
          x-transition:leave="transform ease-in duration-200"
          x-transition:leave-start="translate-x-0 opacity-100"
          x-transition:leave-end="translate-x-64 opacity-0"
          class="fixed top-4 right-4 z-50"
          style="display: none;"
      >
          <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span x-text="message"></span>
          </div>
      </div>
      <div class="mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <input type="text" 
                    wire:model.live="search" 
                    placeholder="Search ..." 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <!-- Filter Section -->
            <div class="flex gap-4 items-center">
                <!-- Date From -->
                <div>
                    <input type="date" 
                        wire:model.live="filters.date_from" 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <!-- Date To -->
                <div>
                    <input type="date" 
                        wire:model.live="filters.date_to" 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <!-- Dropdown -->
                <div>
                    <select 
                        id="status" 
                        name="status" 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        wire:model.live="status">
                        <option value="all" selected>All</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="sent">Sent</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Send ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Send Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap underline text-blue-900">
                                    {{ $campaign->campaign_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap w-32 truncate ">{{ $campaign->send_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap w-32 truncate ">{{ $campaign->customer_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $campaign->customer_phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $campaign->scheduled_date ? $campaign->scheduled_date: '-'}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{$campaign->send_date}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{$campaign->send_status}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No campaigns found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
        @if (session('error'))
            <div class="mb-4 p-4 text-red-600 bg-red-100 border border-red-300 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
        <!-- Konten lainnya -->
        <div class="mt-4">
            {{ $campaigns->links() }}
        </div>
    </div>
    </div>
    
    </div>
    