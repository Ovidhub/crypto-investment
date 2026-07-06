@extends('layouts.dash')
@section('title', $title)
@section('content')
<div class="mt-4 mb-8">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold dark:text-white text-dark">Notifications</h2>
        
        <div class="flex space-x-2">
            @if($notifications->where('is_read', false)->count() > 0)
            <a href="{{ route('notifications.markAll') }}" class="inline-flex items-center text-xs font-medium px-3 py-1.5 rounded-md dark:bg-dark-100 bg-light-200/60 dark:text-gray-300 text-gray-700 dark:hover:bg-dark-100/80 hover:bg-light-200">
                <i class="fas fa-check-circle h-3.5 w-3.5 mr-1"></i>
                <span>Mark all as read</span>
            </a>
            @endif
            
            @if($notifications->count() > 0)
            <a href="{{ route('notifications.deleteAll') }}" onclick="return confirm('Are you sure you want to delete all notifications?')" class="inline-flex items-center text-xs font-medium px-3 py-1.5 rounded-md dark:bg-dark-100 bg-light-200/60 dark:text-gray-300 text-gray-700 dark:hover:bg-dark-100/80 hover:bg-light-200">
                <i class="fas fa-trash-alt h-3.5 w-3.5 mr-1"></i>
                <span>Clear all</span>
            </a>
            @endif
        </div>
    </div>
    
    <div class="mt-6">
        @if($notifications->count() > 0)
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg overflow-hidden">
                @foreach($notifications as $notification)
                <div class="p-4 dark:border-dark-100/60 border-light-200/60 border-b last:border-b-0 {{ $notification->is_read ? '' : 'dark:bg-dark-100/50 bg-light-200/50' }}">
                    <div class="flex items-start gap-4">
                        <div class="{{ $notification->icon_bg_color ?? 'bg-primary/10' }} h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $notification->icon ?? 'fa-bell' }} h-5 w-5 text-primary"></i>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between flex-wrap gap-2">
                                <h3 class="text-sm font-semibold dark:text-white text-dark">{{ $notification->title ?? 'Notification' }}</h3>
                                <time datetime="{{ $notification->created_at }}" class="text-xs dark:text-gray-400 text-gray-500">{{ $notification->created_at->diffForHumans() }}</time>
                            </div>
                            
                            <p class="mt-1 text-sm dark:text-gray-300 text-gray-700">{{ $notification->message }}</p>
                            
                            <div class="mt-3 flex items-center gap-3">
                                @if(!$notification->is_read)
                                <a href="{{ route('notifications.mark', $notification->id) }}" class="inline-flex items-center text-xs font-medium px-2 py-1 rounded dark:bg-primary/20 bg-primary/10 dark:text-primary-light text-primary dark:hover:bg-primary/30 hover:bg-primary/20">
                                    <i class="fas fa-check h-3 w-3 mr-1"></i>
                                    <span>Mark as read</span>
                                </a>
                                @endif
                                
                                @if($notification->link)
                                <a href="{{ route('notifications.mark', $notification->id) }}" class="inline-flex items-center text-xs font-medium px-2 py-1 rounded dark:bg-dark-100 bg-light-200/60 dark:text-gray-300 text-gray-700 dark:hover:bg-dark-100/80 hover:bg-light-200">
                                    <i class="fas fa-external-link-alt h-3 w-3 mr-1"></i>
                                    <span>View details</span>
                                </a>
                                @endif
                                
                                <a href="{{ route('notifications.delete', $notification->id) }}" onclick="return confirm('Are you sure you want to delete this notification?')" class="inline-flex items-center text-xs font-medium px-2 py-1 rounded dark:bg-danger/20 bg-danger/10 dark:text-danger-light text-danger dark:hover:bg-danger/30 hover:bg-danger/20">
                                    <i class="fas fa-trash-alt h-3 w-3 mr-1"></i>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="dark:bg-dark-50 bg-light-100 dark:border-dark-100 border-light-200 border rounded-lg p-6 text-center">
                <div class="h-16 w-16 rounded-full bg-light-200/60 dark:bg-dark-100/60 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash h-8 w-8 dark:text-gray-400 text-gray-600"></i>
                </div>
                <h3 class="text-base font-medium dark:text-white text-dark mb-1">No notifications</h3>
                <p class="text-sm dark:text-gray-400 text-gray-600">You don't have any notifications yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection