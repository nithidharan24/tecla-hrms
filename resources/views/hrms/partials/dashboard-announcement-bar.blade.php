@php
    $announcementAccent     = $announcementAccent     ?? '#E8612C';
    $announcementAccentSoft = $announcementAccentSoft ?? 'rgba(232, 97, 44, 0.10)';
    $announcementItems = collect($announcementItems ?? []);
    // Always show the bar; fall back to a "quiet day" message so it's never empty
    if ($announcementItems->isEmpty()) {
        $announcementItems = collect([
            ['type' => 'Info', 'icon' => 'fa-circle-info', 'text' => 'No events today — have a productive day!'],
        ]);
    }
@endphp

@once
    <style>
        .dashboard-announcement-bar {
            --announcement-accent: #E8612C;
            --announcement-accent-soft: rgba(232, 97, 44, 0.10);
            align-items: center;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 16px;
            box-shadow: 0 14px 34px -24px rgba(15, 23, 42, 0.166), inset 0 1px 0 rgba(255, 255, 255, 0.16);
            display: grid;
            gap: 18px;
            grid-template-columns: auto minmax(0, 1fr);
            margin: 0 0 24px;
            overflow: hidden;
            padding: 13px 16px;
            position: relative;
        }

        .dashboard-announcement-bar::before {
            background: var(--announcement-accent);
            bottom: 14px;
            border-radius: 999px;
            content: '';
            left: 0;
            position: absolute;
            top: 14px;
            width: 4px;
        }

        .dashboard-announcement-label {
            align-items: center;
            background: var(--announcement-accent-soft);
            border: 1px solid color-mix(in srgb, var(--announcement-accent) 22%, transparent);
            border-radius: 12px;
            color: #0F172A;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 9px;
            letter-spacing: .08em;
            line-height: 1;
            padding: 11px 13px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .dashboard-announcement-label i {
            color: var(--announcement-accent);
            font-size: 13px;
        }

        .dashboard-announcement-window {
            min-width: 0;
            overflow: hidden;
            position: relative;
        }

        .dashboard-announcement-window::before,
        .dashboard-announcement-window::after {
            bottom: 0;
            content: '';
            pointer-events: none;
            position: absolute;
            top: 0;
            width: 46px;
            z-index: 2;
        }

        .dashboard-announcement-window::before {
            background: linear-gradient(90deg, #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
            left: 0;
        }

        .dashboard-announcement-window::after {
            background: linear-gradient(270deg, #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
            right: 0;
        }

        .dashboard-announcement-track {
            align-items: center;
            display: flex;
            gap: 14px;
            width: max-content;
            animation: dashboardAnnouncementRun 16s linear infinite;
        }

        .dashboard-announcement-bar:hover .dashboard-announcement-track {
            animation-play-state: paused;
        }

        .dashboard-announcement-group {
            align-items: center;
            display: flex;
            gap: 14px;
        }

        .dashboard-announcement-item {
            align-items: center;
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.92);
            border-radius: 999px;
            box-shadow: 0 8px 18px -16px rgba(15, 23, 42, 0.7);
            color: #334155;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 10px;
            line-height: 1.2;
            padding: 9px 15px 9px 10px;
            white-space: nowrap;
        }

        .dashboard-announcement-icon {
            align-items: center;
            background: var(--announcement-accent-soft);
            border-radius: 50%;
            color: var(--announcement-accent);
            display: inline-flex;
            flex: 0 0 auto;
            height: 28px;
            justify-content: center;
            width: 28px;
        }

        .dashboard-announcement-type {
            color: #0F172A;
            font-weight: 800;
        }

        .dashboard-announcement-separator {
            color: #CBD5E1;
            font-weight: 700;
        }

        @keyframes dashboardAnnouncementRun {
            from { transform: translateX(0); }
            to { transform: translateX(calc(-50% - 7px)); }
        }

        @media (max-width: 767px) {
            .dashboard-announcement-bar {
                gap: 10px;
                grid-template-columns: 1fr;
                padding: 12px;
            }

            .dashboard-announcement-label {
                justify-content: center;
                width: 100%;
            }

            .dashboard-announcement-window::before,
            .dashboard-announcement-window::after {
                width: 22px;
            }

            .dashboard-announcement-track {
                animation-duration: 22s;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-announcement-track {
                animation: none;
                flex-wrap: wrap;
                width: auto;
            }

            .dashboard-announcement-group:last-child {
                display: none;
            }
        }
    </style>
@endonce

<section class="dashboard-announcement-bar" style="--announcement-accent: {{ $announcementAccent }}; --announcement-accent-soft: {{ $announcementAccentSoft }};" aria-label="Company announcements">
    <div class="dashboard-announcement-label">
        <i class="fa fa-bullhorn" aria-hidden="true"></i>
        <span>Announcements</span>
    </div>
    <div class="dashboard-announcement-window">
        <div class="dashboard-announcement-track">
            @for ($loopIndex = 0; $loopIndex < 2; $loopIndex++)
                <div class="dashboard-announcement-group" aria-hidden="{{ $loopIndex === 1 ? 'true' : 'false' }}">
                    @foreach ($announcementItems as $item)
                        <div class="dashboard-announcement-item">
                            <span class="dashboard-announcement-icon"><i class="fa {{ $item['icon'] }}" aria-hidden="true"></i></span>
                            <span class="dashboard-announcement-type">{{ $item['type'] }}</span>
                            <span class="dashboard-announcement-separator">/</span>
                            <span>{{ $item['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</section>
