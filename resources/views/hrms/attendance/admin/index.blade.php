@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

$modules = [];

if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();

} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp
@extends('layouts.index')

@section('content')
<style>
/* ============================================================
   Attendance — ELITE v3  |  Tecla HRMS
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.attendance-page {
    --o: #ff6a00;
    --o-deep: #e55a00;
    --o-glow: rgba(255,106,0,.22);
    --o-soft: #fff3ea;
    --o-softer: #fff8f3;
    --o-line: #ffd9b8;
    --green: #16a34a;
    --green-soft: #ecfdf5;
    --green-line: #bbf7d0;
    --red: #dc2626;
    --red-soft: #fef2f2;
    --red-line: #fecaca;
    --amber: #b45309;
    --amber-soft: #fffbeb;
    --amber-line: #fde68a;
    --blue: #2563eb;
    --blue-soft: #eff6ff;
    --blue-line: #bfdbfe;
    --ink: #0f1117;
    --ink-2: #3d4256;
    --ink-3: #8892a4;
    --ink-4: #c1c8d4;
    --line: #eaecf0;
    --line-2: #f4f5f7;
    --bg: #f6f7fb;
    --card: #ffffff;
    --r-xl: 22px;
    --r-lg: 16px;
    --r-md: 12px;
    --r-sm: 8px;
    --sh-sm: 0 1px 4px rgba(15,17,23,.06), 0 4px 12px rgba(15,17,23,.04);
    --sh-md: 0 4px 16px rgba(15,17,23,.07), 0 12px 32px rgba(15,17,23,.05);
    --sh-pop: 0 8px 32px rgba(255,106,0,.20), 0 2px 8px rgba(255,106,0,.12);
    font-family: 'Plus Jakarta Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    color: var(--ink);
    background: var(--bg);
    padding-bottom: 3rem;
    -webkit-font-smoothing: antialiased;
}
.attendance-page *, .att-modal *, .att-dropdown * { box-sizing: border-box; margin: 0; }

/* ===== HERO ===== */
.attendance-page .att-hero {
    position: relative; border-radius: var(--r-xl);
    background: var(--card); border: 1px solid var(--line);
    padding: 2rem 2.25rem; margin-bottom: 1.5rem;
    box-shadow: var(--sh-sm);
}
.attendance-page .att-hero-bg {
    position: absolute; inset: 0; pointer-events: none; z-index: 0;
    border-radius: var(--r-xl); overflow: hidden;
    background:
      radial-gradient(ellipse 800px 300px at -5% -60%, rgba(255,106,0,.09), transparent),
      radial-gradient(ellipse 600px 250px at 105% 140%, rgba(255,106,0,.07), transparent),
      url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6a00' fill-opacity='0.025'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.attendance-page .att-hero-inner {
    position: relative; z-index: 1;
    display: flex; flex-wrap: wrap; gap: 1.5rem;
    align-items: center; justify-content: space-between;
}
.attendance-page .att-crumbs {
    list-style: none; padding: 0; margin: 0 0 .55rem;
    display: flex; gap: .4rem; align-items: center;
    font-size: .75rem; color: var(--ink-3); font-weight: 600;
}
.attendance-page .att-crumbs a { color: var(--o); text-decoration: none; transition: opacity .15s; }
.attendance-page .att-crumbs a:hover { opacity: .75; }
.attendance-page .att-crumbs .sep { color: var(--ink-4); }
.attendance-page .att-crumbs .active { color: var(--ink-2); }
.attendance-page .att-hero-title {
    font-size: 2.1rem; font-weight: 800; letter-spacing: -0.04em;
    color: var(--ink); line-height: 1.1; margin: 0;
    background: linear-gradient(135deg, var(--ink) 40%, var(--o-deep));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.attendance-page .att-hero-sub {
    margin: .5rem 0 0; color: var(--ink-3); font-size: .9rem; max-width: 480px;
    font-weight: 500;
}
.attendance-page .att-hero-right { display: flex; flex-wrap: wrap; gap: .55rem; align-items: center; }

/* ===== BUTTONS ===== */
.attendance-page .att-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .62rem 1.15rem; border-radius: 999px;
    font-size: .82rem; font-weight: 700; letter-spacing: -.01em;
    border: 1.5px solid transparent; cursor: pointer;
    transition: all .2s cubic-bezier(.4,0,.2,1);
    line-height: 1; white-space: nowrap; text-decoration: none;
}
.attendance-page .att-btn i { font-size: .82rem; }
.attendance-page .att-btn-solid {
    background: linear-gradient(135deg, var(--o), #ff8c38);
    color: #fff; border-color: transparent;
    box-shadow: var(--sh-pop);
}
.attendance-page .att-btn-solid:hover {
    background: linear-gradient(135deg, var(--o-deep), var(--o));
    transform: translateY(-2px); box-shadow: 0 12px 36px var(--o-glow); color: #fff;
}
.attendance-page .att-btn-ghost {
    background: var(--card); color: var(--ink-2); border-color: var(--line);
}
.attendance-page .att-btn-ghost:hover {
    border-color: var(--o); color: var(--o); background: var(--o-soft);
    transform: translateY(-1px); box-shadow: var(--sh-sm);
}
.attendance-page .att-btn-group { position: relative; display: inline-block; }
.att-dropdown {
    border-radius: var(--r-lg); border: 1px solid var(--line);
    box-shadow: 0 20px 60px rgba(15,17,23,.12); padding: .4rem; min-width: 210px;
    animation: dropIn .18s ease;
}
@keyframes dropIn { from { opacity:0; transform: translateY(-8px) scale(.97); } to { opacity:1; transform: none; } }
.att-dropdown .dropdown-item {
    border-radius: 10px; font-size: .84rem; padding: .6rem .85rem;
    display: flex; align-items: center; gap: .6rem; color: var(--ink); font-weight: 600;
    transition: background .15s, color .15s;
}
.att-dropdown .dropdown-item i { width: 16px; color: var(--o); flex-shrink: 0; }
.att-dropdown .dropdown-item:hover { background: var(--o-soft); color: var(--o); }

/* ===== ALERTS ===== */
.attendance-page .att-alert {
    border-radius: var(--r-lg); border: 1px solid; font-size: .87rem;
    padding: .9rem 1.1rem; display: flex; align-items: center; gap: .6rem;
}
.attendance-page .att-alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
.attendance-page .att-alert-danger  { background: var(--red-soft); border-color: var(--red-line); color: var(--red); }

/* ===== KPI ROW ===== */
.attendance-page .kpi-row {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 1rem; margin-bottom: 1.5rem;
}
.attendance-page .kpi {
    position: relative; background: var(--card); border: 1px solid var(--line);
    border-radius: var(--r-lg); padding: 1.35rem 1.45rem;
    display: flex; align-items: flex-start; gap: 1rem; overflow: hidden;
    transition: all .25s cubic-bezier(.4,0,.2,1);
    cursor: default;
}
.attendance-page .kpi::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, transparent 60%, rgba(255,106,0,.03));
    pointer-events: none;
}
.attendance-page .kpi:hover { transform: translateY(-4px); box-shadow: var(--sh-md); border-color: var(--o-line); }
.attendance-page .kpi-accent {
    position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    border-radius: 4px 0 0 4px; background: var(--o-line);
    transition: background .25s;
}
.attendance-page .kpi:hover .kpi-accent { background: linear-gradient(180deg, var(--o), #ffb067); }
.attendance-page .kpi-icon {
    width: 46px; height: 46px; flex-shrink: 0;
    border-radius: var(--r-md); display: inline-flex; align-items: center; justify-content: center;
    background: var(--o-soft); color: var(--o); font-size: 1.2rem;
    box-shadow: inset 0 1px 2px rgba(255,255,255,.8);
}
.attendance-page .kpi-body { flex: 1; min-width: 0; }
.attendance-page .kpi-label {
    display: block; font-size: .67rem; font-weight: 700;
    color: var(--ink-3); text-transform: uppercase; letter-spacing: .1em;
}
.attendance-page .kpi-value {
    font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em;
    color: var(--ink); line-height: 1.1; margin-top: .2rem;
}
.attendance-page .kpi-unit { font-size: 1rem; font-weight: 600; color: var(--ink-3); }
.attendance-page .kpi-foot { display: block; font-size: .7rem; color: var(--ink-3); margin-top: .2rem; font-weight: 500; }
.attendance-page .kpi-bar { height: 5px; border-radius: 99px; background: var(--o-soft); margin-top: .6rem; overflow: hidden; }
.attendance-page .kpi-bar span { display: block; height: 100%; background: linear-gradient(90deg, var(--o), #ffb067); border-radius: 99px; transition: width 1s ease; }

.attendance-page .kpi-feature {
    background: linear-gradient(135deg, var(--o) 0%, #ff8c38 50%, #ffa050 100%);
    border-color: transparent; box-shadow: var(--sh-pop);
}
.attendance-page .kpi-feature::before { background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%); }
.attendance-page .kpi-feature .kpi-accent { background: rgba(255,255,255,.3); }
.attendance-page .kpi-feature:hover .kpi-accent { background: rgba(255,255,255,.6); }
.attendance-page .kpi-feature .kpi-icon { background: rgba(255,255,255,.2); color: #fff; box-shadow: none; }
.attendance-page .kpi-feature .kpi-label,
.attendance-page .kpi-feature .kpi-value,
.attendance-page .kpi-feature .kpi-foot,
.attendance-page .kpi-feature .kpi-unit { color: #fff; }
.attendance-page .kpi-feature .kpi-foot { opacity: .85; }

/* ===== COMMAND BAR ===== */
.attendance-page .cmd-bar {
    display: flex; gap: .55rem; align-items: stretch;
    background: var(--card); border: 1px solid var(--line);
    border-radius: 999px; padding: .45rem .5rem; margin-bottom: 1.5rem;
    box-shadow: var(--sh-sm); flex-wrap: wrap;
}
.attendance-page .cmd-field {
    position: relative; display: flex; align-items: center; gap: .5rem;
    padding: 0 1rem; border-radius: 999px; background: var(--line-2);
    border: 1.5px solid transparent; min-width: 175px; transition: all .2s;
}
.attendance-page .cmd-field.grow { flex: 1; min-width: 220px; }
.attendance-page .cmd-field i { color: var(--ink-3); font-size: .82rem; flex-shrink: 0; }
.attendance-page .cmd-field select {
    border: 0; background: transparent; outline: none;
    font-size: .88rem; font-weight: 600; color: var(--ink);
    padding: .72rem 1.8rem .72rem 0; width: 100%;
    appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path fill='%238892a4' d='M5 6L0 0h10z'/></svg>");
    background-repeat: no-repeat; background-position: right .3rem center;
}
.attendance-page .cmd-field:focus-within { background: var(--o-soft); border-color: var(--o-line); }
.attendance-page .cmd-field:focus-within i { color: var(--o); }
.attendance-page .cmd-go { padding: .72rem 1.6rem; border-radius: 999px; }

/* ===== PANEL SHELL ===== */
.attendance-page .panel {
    background: var(--card); border: 1px solid var(--line);
    border-radius: var(--r-xl); overflow: hidden;
    box-shadow: var(--sh-sm);
}
.attendance-page .panel-head {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--line);
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
    background: linear-gradient(180deg, #ffffff 0%, #fefaf6 100%);
}
.attendance-page .panel-title {
    font-size: 1.1rem; font-weight: 800; letter-spacing: -0.025em; color: var(--ink);
    display: flex; align-items: center; gap: .6rem;
}
.attendance-page .panel-title .title-badge {
    font-size: .65rem; font-weight: 700; background: var(--o-soft);
    color: var(--o); border: 1px solid var(--o-line);
    padding: .15rem .5rem; border-radius: 999px; letter-spacing: .04em;
    text-transform: uppercase;
}
.attendance-page .panel-sub {
    margin: .2rem 0 0; font-size: .75rem; color: var(--ink-3);
    display: flex; align-items: center; gap: .35rem; font-weight: 500;
}
.attendance-page .panel-sub i { color: var(--o); }

/* Legend — pill-style */
.attendance-page .panel-legend { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
.attendance-page .panel-legend .lg {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .7rem; font-weight: 700; color: var(--ink-2);
    background: var(--line-2); border: 1px solid var(--line);
    padding: .3rem .7rem .3rem .45rem; border-radius: 999px;
    transition: all .15s;
}
.attendance-page .panel-legend .lg:hover { border-color: var(--o-line); background: var(--o-soft); color: var(--o); }
.attendance-page .panel-body { padding: 0; }

/* ===== TABLE WRAPPER ===== */
.attendance-page .att-table-wrap {
    max-height: 70vh; overflow: auto; position: relative;
}
.attendance-page .att-table-wrap::-webkit-scrollbar { height: 6px; width: 6px; }
.attendance-page .att-table-wrap::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, var(--o-line), #ffb067);
    border-radius: 99px;
}
.attendance-page .att-table-wrap::-webkit-scrollbar-thumb:hover { background: var(--o); }
.attendance-page .att-table-wrap::-webkit-scrollbar-track { background: var(--line-2); }

/* ===== ATTENDANCE TABLE ===== */
.attendance-page .attendance-table {
    font-size: .78rem; margin: 0;
    border-collapse: separate; border-spacing: 0; width: 100%;
}
.attendance-page .attendance-table thead th {
    background: #fafbfc; color: var(--ink-3);
    font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
    padding: .8rem .3rem; text-align: center;
    border-bottom: 2px solid var(--line);
    position: sticky; top: 0; z-index: 5;
}
.attendance-page .attendance-table tbody td {
    vertical-align: middle; padding: .55rem .3rem; text-align: center;
    border-bottom: 1px solid #f2f3f7; background: var(--card);
    transition: background .12s;
}
.attendance-page .attendance-table tbody tr:last-child td { border-bottom: 0; }

/* Zebra + hover */
.attendance-page .attendance-table tbody tr:nth-child(even) td { background: #fdfefe; }
.attendance-page .attendance-table tbody tr:hover td { background: #fff8f3 !important; }
.attendance-page .attendance-table tbody tr:hover td.sticky-col { background: #fff3ea !important; }
.attendance-page .attendance-table tbody tr:hover td.sticky-right { background: #fff3ea !important; }

/* ===== DAY COLUMN HEADERS ===== */
.attendance-page .day-head {
    min-width: 50px; padding: .6rem .25rem !important;
    transition: background .15s;
}
.attendance-page .day-head .day-num {
    display: block; font-size: .95rem; font-weight: 800;
    color: var(--ink); letter-spacing: -0.03em; line-height: 1;
}
.attendance-page .day-head .day-name {
    display: block; font-size: .58rem; color: var(--ink-3);
    margin-top: .2rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
}

/* Weekend col */
.attendance-page .day-head.is-weekend { background: #f8f9fb; }
.attendance-page .day-head.is-weekend .day-num { color: var(--ink-4); }
.attendance-page .day-head.is-weekend .day-name { color: var(--ink-4); }

/* Holiday col */
.attendance-page .day-head.is-holiday { background: #fff6ef; }
.attendance-page .day-head.is-holiday .day-num { color: var(--o); }

/* Today col */
.attendance-page .day-head.is-today {
    background: linear-gradient(160deg, var(--o) 0%, #ff9440 100%);
    box-shadow: inset 0 -3px 0 rgba(0,0,0,.12);
}
.attendance-page .day-head.is-today .day-num,
.attendance-page .day-head.is-today .day-name { color: #fff !important; }

/* Indicator dots */
.attendance-page .holiday-dot,
.attendance-page .today-dot {
    display: block; width: 5px; height: 5px;
    border-radius: 50%; margin: .25rem auto 0;
}
.attendance-page .holiday-dot { background: var(--o); }
.attendance-page .today-dot { background: rgba(255,255,255,.85); }

/* ===== STICKY COLUMNS ===== */
.attendance-page .sticky-col {
    position: sticky; left: 0; z-index: 4;
    min-width: 255px; max-width: 255px; text-align: left !important;
    background: var(--card);
    border-right: 1px solid var(--line);
}
.attendance-page .attendance-table thead th.sticky-col { z-index: 7; border-right: 1px solid var(--line); }

.attendance-page .sticky-right {
    position: sticky; right: 0; z-index: 4;
    background: var(--card);
    border-left: 1px solid var(--line);
}
.attendance-page .attendance-table thead th.sticky-right { z-index: 7; border-left: 1px solid var(--line); }

/* ===== EMPLOYEE CELL ===== */
.attendance-page .emp-cell {
    display: flex; align-items: center; gap: .9rem; padding: .3rem .5rem .3rem 1rem;
}
.attendance-page .emp-avatar {
    position: relative; flex-shrink: 0;
    width: 40px; height: 40px; border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .95rem; letter-spacing: -0.02em; color: #fff;
    background: linear-gradient(135deg, var(--o) 0%, #ff9040 100%);
    box-shadow: 0 4px 12px var(--o-glow);
}
.attendance-page .emp-avatar .emp-dot {
    position: absolute; right: -2px; bottom: -2px;
    width: 11px; height: 11px; border-radius: 50%;
    background: #22c55e; border: 2px solid var(--card);
}
.attendance-page .emp-info { min-width: 0; flex: 1; }
.attendance-page .emp-name {
    font-size: .88rem; font-weight: 700; color: var(--ink);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    letter-spacing: -0.015em; line-height: 1.3;
}
.attendance-page .emp-id {
    font-size: .68rem; color: var(--ink-3); font-weight: 600; margin-bottom: .2rem;
}
.attendance-page .emp-meta {
    display: flex; flex-wrap: wrap; gap: .25rem; align-items: center; margin-top: .2rem;
}
.attendance-page .chip {
    display: inline-flex; align-items: center;
    font-size: .62rem; font-weight: 700; padding: .18rem .5rem;
    border-radius: 999px; letter-spacing: .03em; line-height: 1;
    border: 1px solid transparent;
}
.attendance-page .chip-orange { background: var(--o-soft); color: var(--o); border-color: var(--o-line); }
.attendance-page .chip-muted  { background: var(--line-2); color: var(--ink-2); border-color: var(--line); }

/* ===== ATTENDANCE PILLS ===== */
.attendance-page .att-pill {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; padding: 0;
    border-radius: 9px; font-size: .75rem; font-weight: 800;
    border: 1.5px solid transparent;
    transition: transform .15s cubic-bezier(.4,0,.2,1), box-shadow .15s;
    position: relative;
}
.attendance-page .att-pill.cursor-pointer { cursor: pointer; }
.attendance-page .att-pill.cursor-pointer:hover {
    transform: scale(1.18) translateY(-1px);
    box-shadow: 0 6px 18px rgba(0,0,0,.15);
    z-index: 2;
}
.attendance-page .pill-present {
    background: var(--green-soft); color: var(--green); border-color: var(--green-line);
}
.attendance-page .pill-partial {
    background: var(--amber-soft); color: var(--amber); border-color: var(--amber-line);
}
.attendance-page .pill-absent {
    background: var(--red-soft); color: var(--red); border-color: var(--red-line);
}
.attendance-page .pill-holiday {
    background: var(--o-soft); color: var(--o); border-color: var(--o-line);
}
.attendance-page .pill-leave {
    background: var(--blue-soft); color: var(--blue); border-color: var(--blue-line);
}
.attendance-page .att-dash {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; color: var(--ink-4); font-size: .7rem;
}

/* Cell background tints */
.attendance-page .working-day { background: rgba(255,106,0,.015); }
.attendance-page .employee-weekend { background: #f9fafb; }
.attendance-page .employee-leave { background: rgba(37,99,235,.03); }

/* ===== SUMMARY CELL — ELITE v3 ===== */
.attendance-page .summary-head {
    min-width: 280px !important; text-align: center !important; padding: .8rem 1rem !important;
    background: linear-gradient(180deg, #fafbfc, #f5f6f8) !important;
}
.attendance-page .summary-cell {
    padding: .4rem .65rem !important; vertical-align: middle !important; text-align: left !important;
}

/* The card itself */
.attendance-page .sum-card {
    display: grid;
    grid-template-columns: 60px 1fr auto;
    grid-template-rows: auto auto;
    gap: 0 .75rem;
    align-items: center;
    background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
    border: 1px solid var(--line);
    border-radius: var(--r-lg); padding: .6rem .75rem;
    min-width: 265px;
    transition: all .22s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.attendance-page .sum-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
    background: var(--line); border-radius: 3px 0 0 3px; transition: background .22s;
}
.attendance-page .attendance-table tbody tr:hover .sum-card {
    box-shadow: 0 4px 20px rgba(255,106,0,.14), 0 1px 4px rgba(255,106,0,.08);
    border-color: var(--o-line);
    background: linear-gradient(135deg, var(--o-softer) 0%, #fff 100%);
    transform: translateX(-2px);
}
.attendance-page .attendance-table tbody tr:hover .sum-card::before {
    background: linear-gradient(180deg, var(--o), #ffb067);
}

/* Donut */
.attendance-page .sum-donut-wrap {
    position: relative; width: 56px; height: 56px;
    grid-row: 1 / 3; grid-column: 1;
    display: flex; align-items: center; justify-content: center;
}
.attendance-page .sum-donut {
    width: 56px; height: 56px; transform: rotate(-90deg);
    filter: drop-shadow(0 2px 6px rgba(255,106,0,.15));
}
.attendance-page .donut-track { stroke: var(--line); }
.attendance-page .donut-fill {
    stroke: var(--o); stroke-linecap: round;
    transition: stroke-dasharray .8s cubic-bezier(.4,0,.2,1);
}
.attendance-page .sum-pct {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    line-height: 1;
}
.attendance-page .sum-pct-num {
    font-size: .82rem; font-weight: 800; color: var(--ink); letter-spacing: -0.03em;
}
.attendance-page .sum-pct-unit {
    font-size: .52rem; font-weight: 700; color: var(--ink-3); margin-top: .5px;
}

/* 4-stat row */
.attendance-page .sum-stats-row {
    display: flex; gap: .3rem;
    grid-column: 2; grid-row: 1;
    align-items: stretch;
}
.attendance-page .ssi {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    border-radius: 8px; padding: .3rem .45rem; min-width: 46px; flex: 1;
    border: 1px solid transparent; transition: all .15s;
}
.attendance-page .ssi:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.08); }
.attendance-page .ssi-val {
    font-size: .95rem; font-weight: 800; line-height: 1; letter-spacing: -0.03em;
}
.attendance-page .ssi-lbl {
    font-size: .58rem; font-weight: 700; margin-top: .2rem;
    text-transform: uppercase; letter-spacing: .04em; opacity: .75;
}
.attendance-page .ssi-present { background: var(--green-soft); color: var(--green); border-color: var(--green-line); }
.attendance-page .ssi-absent  { background: var(--red-soft); color: var(--red); border-color: var(--red-line); }
.attendance-page .ssi-late    { background: var(--amber-soft); color: var(--amber); border-color: var(--amber-line); }
.attendance-page .ssi-leave   { background: var(--blue-soft); color: var(--blue); border-color: var(--blue-line); }

/* Hours row */
.attendance-page .sum-hours-row {
    grid-column: 2 / 3; grid-row: 2;
    display: flex; align-items: center; gap: .3rem;
    margin-top: .3rem;
}
.attendance-page .sum-hours-row i { color: var(--o); font-size: .7rem; }
.attendance-page .sum-hours-val {
    font-size: .72rem; font-weight: 700; color: var(--ink-2); letter-spacing: -0.01em;
}
.attendance-page .sum-hours-bar {
    flex: 1; height: 4px; background: var(--line); border-radius: 99px; overflow: hidden;
}
.attendance-page .sum-hours-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--o), #ffb067);
    border-radius: 99px;
    transition: width 1s ease;
}

/* ===== MODAL INPUTS ===== */
.att-modal .att-label,
.attendance-page .att-label {
    font-size: .7rem; font-weight: 700; color: var(--ink-2);
    margin-bottom: .4rem; text-transform: uppercase; letter-spacing: .07em; display: block;
}
.att-modal .att-input,
.attendance-page .att-input {
    border-radius: var(--r-md); border: 1.5px solid var(--line);
    padding: .68rem .9rem; font-size: .88rem; color: var(--ink);
    background: var(--card); transition: all .2s; width: 100%;
}
.att-modal .att-input:focus,
.attendance-page .att-input:focus {
    border-color: var(--o); box-shadow: 0 0 0 3px rgba(255,106,0,.12); outline: none;
}

/* ===== MODALS ===== */
.att-modal .modal-content {
    border-radius: var(--r-xl); border: none;
    box-shadow: 0 30px 100px rgba(15,17,23,.2); overflow: hidden;
}
.att-modal .modal-header {
    border-bottom: 1px solid #f0e8df; padding: 1.2rem 1.6rem;
    background: linear-gradient(180deg, #fff, #fffaf5);
}
.att-modal .modal-title {
    font-weight: 800; color: var(--ink); letter-spacing: -0.025em;
    font-size: 1.05rem; display: flex; align-items: center; gap: .5rem;
}
.att-modal .modal-title i { color: var(--o); }
.att-modal .modal-body { padding: 1.6rem; }
.att-modal .modal-footer { border-top: 1px solid #f0e8df; padding: 1rem 1.6rem; gap: .55rem; }
.att-modal .text-orange { color: var(--o) !important; }
.att-modal .spinner-border.text-orange { color: var(--o) !important; }

/* ===== VIEW SWITCHER ===== */
.attendance-page .view-switcher {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem; margin-bottom: 1.25rem;
}
.attendance-page .vs-tabs {
    display: flex; gap: .25rem;
    background: var(--card); border: 1px solid var(--line);
    border-radius: 999px; padding: .25rem;
    box-shadow: var(--sh-sm);
}
.attendance-page .vs-tab {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem 1.1rem; border-radius: 999px; border: none;
    font-size: .82rem; font-weight: 700; cursor: pointer;
    color: var(--ink-3); background: transparent; transition: all .2s;
}
.attendance-page .vs-tab:hover { color: var(--o); background: var(--o-soft); }
.attendance-page .vs-tab.vs-active {
    background: linear-gradient(135deg, var(--o), #ff8c38);
    color: #fff; box-shadow: 0 4px 12px var(--o-glow);
}
.attendance-page .vs-week-nav {
    display: flex; align-items: center; gap: .5rem;
}
.attendance-page .vs-week-label {
    font-size: .85rem; font-weight: 700; color: var(--ink);
    padding: .4rem .9rem; background: var(--o-soft);
    border: 1px solid var(--o-line); border-radius: 999px;
    min-width: 180px; text-align: center;
}
.attendance-page .vs-nav-btn {
    width: 34px; height: 34px; border-radius: 50%;
    border: 1.5px solid var(--line); background: var(--card);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: .8rem; color: var(--ink-2); font-weight: 700;
    transition: all .2s;
}
.attendance-page .vs-nav-btn:hover { border-color: var(--o); color: var(--o); background: var(--o-soft); }
.attendance-page .vs-today { width: auto; padding: 0 .85rem; border-radius: 999px; font-size: .78rem; }

/* ===== MOBILE / DESKTOP TOGGLE ===== */
.attendance-page .desktop-view { display: block; }
.attendance-page .mobile-view  { display: none; }

/* ===== MOBILE CARDS ===== */
.attendance-page .mob-card {
    background: var(--card); border: 1px solid var(--line);
    border-radius: var(--r-lg); margin: .75rem .5rem;
    padding: 1rem; box-shadow: var(--sh-sm);
    transition: box-shadow .2s;
}
.attendance-page .mob-card:hover { box-shadow: var(--sh-md); }
.attendance-page .mob-card-header {
    display: flex; align-items: center; gap: .75rem; margin-bottom: .85rem;
}
.attendance-page .mob-emp-info { flex: 1; min-width: 0; }
.attendance-page .mob-emp-name {
    font-size: .92rem; font-weight: 700; color: var(--ink); white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.attendance-page .mob-emp-sub { font-size: .72rem; color: var(--ink-3); margin-top: .15rem; }
.attendance-page .mob-pct { font-size: 1.3rem; font-weight: 800; letter-spacing: -0.03em; flex-shrink: 0; }

.attendance-page .mob-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: .3rem; margin-bottom: .85rem;
    overflow-x: auto;
}
.attendance-page .mob-day {
    display: flex; flex-direction: column; align-items: center; gap: .25rem;
    min-width: 38px;
}
.attendance-page .mob-day-label {
    font-size: .55rem; font-weight: 700; color: var(--ink-3);
    text-transform: uppercase; letter-spacing: .04em; text-align: center;
    white-space: nowrap;
}
.attendance-page .mob-stats {
    display: flex; gap: .5rem; flex-wrap: wrap;
    border-top: 1px solid var(--line); padding-top: .7rem;
}
.attendance-page .mob-stat {
    display: flex; flex-direction: column; align-items: center;
    flex: 1; min-width: 48px;
}
.attendance-page .mob-stat-val { font-size: .95rem; font-weight: 800; line-height: 1; }
.attendance-page .mob-stat-lbl { font-size: .6rem; font-weight: 700; color: var(--ink-3); margin-top: .15rem; text-transform: uppercase; }

@media (max-width: 768px) {
    .attendance-page .desktop-view { display: none !important; }
    .attendance-page .mobile-view  { display: block !important; }
    .attendance-page .mob-days { grid-template-columns: repeat(auto-fill, minmax(36px, 1fr)); }
    .attendance-page .view-switcher { flex-direction: column; align-items: flex-start; }
    .attendance-page .vs-week-nav { width: 100%; justify-content: space-between; }
    .attendance-page .vs-week-label { flex: 1; text-align: center; font-size: .78rem; min-width: unset; }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1200px) {
    .attendance-page .sum-card { min-width: 230px; }
}
@media (max-width: 1100px) {
    .attendance-page .kpi-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 900px) {
    .attendance-page .sum-hours-row { display: none; }
    .attendance-page .sum-card { min-width: 180px; }
    .attendance-page .ssi-lbl { display: none; }
}
@media (max-width: 768px) {
    .attendance-page .att-hero { padding: 1.4rem 1.25rem; border-radius: 18px; }
    .attendance-page .att-hero-title { font-size: 1.6rem; }
    .attendance-page .att-hero-right { width: 100%; }
    .attendance-page .att-btn span { display: none; }
    .attendance-page .kpi-row { grid-template-columns: repeat(2, 1fr); }
    .attendance-page .cmd-bar { border-radius: 18px; flex-direction: column; padding: .65rem; }
    .attendance-page .cmd-field { min-width: 100%; }
    .attendance-page .cmd-go { width: 100%; justify-content: center; }
    .attendance-page .panel-legend { display: none; }
    .attendance-page .sum-card { min-width: 150px; gap: .3rem; }
}
</style>
<div class="content container-fluid attendance-page">

    {{-- ============== HERO BAND ============== --}}
    <div class="att-hero">
        <div class="att-hero-bg"></div>
        <div class="att-hero-inner">
            <div class="att-hero-left">
                <ul class="att-crumbs">
                    <li><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="sep">/</li>
                    <li class="active">Attendance</li>
                </ul>
                <h1 class="att-hero-title">Attendance Management</h1>
                <p class="att-hero-sub">Track presence, leaves, overtime &amp; punctuality — all in one elite workspace.</p>
            </div>
            <div class="att-hero-right">
              
                @if( in_array('Late Punch Approval', $modules))
                <a href="{{ route('admin.manual-punch.index') }}" class="att-btn att-btn-ghost">
                    <i class="fa-solid fa-hand"></i><span>Manual Punch</span>
                </a>
                @endif
                <div class="att-btn-group">
                    <button type="button" class="att-btn att-btn-ghost dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-download"></i><span>Export</span>
                    </button>
                    <ul class="dropdown-menu att-dropdown">
                        <li><a class="dropdown-item" href="#" onclick="exportAttendance('excel')"><i class="fa-solid fa-file-excel"></i> Excel (.xlsx)</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportAttendance('csv')"><i class="fa-solid fa-file-csv"></i> CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportAttendance('pdf')"><i class="fa-solid fa-file-pdf"></i> PDF</a></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert att-alert att-alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert att-alert att-alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ============== KPI ROW ============== --}}
    <div class="kpi-row">
        <div class="kpi kpi-feature">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Total Employees</span>
                <div class="kpi-value">{{ $summaryStats['total_employees'] }}</div>
                <span class="kpi-foot">Active workforce</span>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa-solid fa-chart-line"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Attendance Rate</span>
                <div class="kpi-value">{{ $summaryStats['overall_attendance_rate'] }}<span class="kpi-unit">%</span></div>
                <div class="kpi-bar"><span style="width: {{ min(100, (float)$summaryStats['overall_attendance_rate']) }}%"></span></div>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa-solid fa-business-time"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Overtime Hours</span>
                <div class="kpi-value">{{ $summaryStats['total_overtime_hours'] }}<span class="kpi-unit">h</span></div>
                <span class="kpi-foot">This month</span>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-accent"></div>
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Late Instances</span>
                <div class="kpi-value">{{ $summaryStats['total_late_days'] }}</div>
                <span class="kpi-foot">Punctuality flags</span>
            </div>
        </div>
    </div>

    {{-- ============== VIEW TOGGLE ============== --}}
    @php
        $viewMode = request('view', 'monthly');
        $weekOffset = (int) request('week_offset', 0);
        $todayCarbon = \Carbon\Carbon::today();
        $weekStart = $todayCarbon->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd   = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
    @endphp

    {{-- ============== COMMAND BAR FILTER ============== --}}
    <form action="{{ route('admin.attendance.index') }}" method="GET" id="attendanceFilterForm" class="cmd-bar">
        <input type="hidden" name="view" id="view_mode" value="{{ $viewMode }}">
        <input type="hidden" name="week_offset" id="week_offset" value="{{ $weekOffset }}">
        <div class="cmd-field">
            <i class="fa-regular fa-calendar"></i>
            <select id="month" name="month">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="cmd-field">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <select id="year" name="year">
                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="cmd-field grow">
            <i class="fa-solid fa-building"></i>
            <select id="department_id" name="department_id">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                @endforeach
            </select>
        </div>
        <div class="cmd-field grow">
            <i class="fa-solid fa-user"></i>
            <select id="employee_id" name="employee_id">
                <option value="">All Employees</option>
                @foreach($employeeOptions as $option)
                    <option value="{{ $option->id }}" {{ (int)$selectedEmployee === $option->id ? 'selected' : '' }}>{{ $option->firstname }} {{ $option->lastname }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="att-btn att-btn-solid cmd-go">
            <i class="fa-solid fa-magnifying-glass"></i><span>Search</span>
        </button>
    </form>

    {{-- ============== VIEW SWITCHER ============== --}}
    <div class="view-switcher">
        <div class="vs-tabs">
            <button type="button" class="vs-tab {{ $viewMode === 'monthly' ? 'vs-active' : '' }}" onclick="switchView('monthly')">
                <i class="fa-solid fa-calendar"></i> Monthly
            </button>
            <button type="button" class="vs-tab {{ $viewMode === 'weekly' ? 'vs-active' : '' }}" onclick="switchView('weekly')">
                <i class="fa-solid fa-calendar-week"></i> Weekly
            </button>
        </div>
        @if($viewMode === 'weekly')
        <div class="vs-week-nav">
            <button type="button" class="vs-nav-btn" onclick="changeWeek(-1)">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <span class="vs-week-label">
                {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
            </span>
            <button type="button" class="vs-nav-btn" onclick="changeWeek(1)">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <button type="button" class="vs-nav-btn vs-today" onclick="changeWeek(0, true)">
                Today
            </button>
        </div>
        @endif
    </div>

    {{-- ============== CALENDAR PANEL ============== --}}
    @php
        // Determine which days to show
        if ($viewMode === 'weekly') {
            $viewDays = [];
            $cursor = $weekStart->copy();
            while ($cursor->lte($weekEnd)) {
                if ($cursor->month == $month && $cursor->year == $year) {
                    $viewDays[] = $cursor->day;
                } elseif ($cursor->month != $month || $cursor->year != $year) {
                    // show cross-month days in weekly view
                    $viewDays[] = ['date' => $cursor->format('Y-m-d'), 'day' => $cursor->day,
                                   'month' => $cursor->month, 'year' => $cursor->year];
                }
                $cursor->addDay();
            }
            // Rebuild as full date objects
            $weekDays = [];
            $c2 = $weekStart->copy();
            for ($w = 0; $w < 7; $w++) {
                $weekDays[] = $c2->copy();
                $c2->addDay();
            }
        }
    @endphp
    <section class="panel">
        <header class="panel-head">
            <div>
                <h2 class="panel-title">
                    @if($viewMode === 'weekly')
                        Week of {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
                        <span class="title-badge">Weekly View</span>
                    @else
                        {{ $monthName }} {{ $year }}
                        <span class="title-badge">Monthly View</span>
                    @endif
                </h2>
                <p class="panel-sub"><i class="fa-solid fa-circle-info"></i> Working days appear only when the date is within an active schedule and the weekday is allowed.</p>
            </div>
            <div class="panel-legend">
                <span class="lg"><span class="att-pill pill-present"><i class="fa-solid fa-check"></i></span>Present</span>
                <span class="lg"><span class="att-pill pill-partial"><i class="fa-solid fa-exclamation"></i></span>Partial</span>
                <span class="lg"><span class="att-pill pill-absent"><i class="fa-solid fa-xmark"></i></span>Absent</span>
                <span class="lg"><span class="att-pill pill-leave"><i class="fa-solid fa-calendar-xmark"></i></span>Leave</span>
                <span class="lg"><span class="att-pill pill-holiday"><i class="fa-solid fa-umbrella-beach"></i></span>Holiday</span>
                <span class="lg"><span class="att-dash"><i class="fa-solid fa-minus"></i></span>Off</span>
            </div>
        </header>

        <div class="panel-body">
            {{-- ===== DESKTOP TABLE (monthly) ===== --}}
            <div class="att-table-wrap desktop-view">
                <table class="table attendance-table mb-0">
                    <thead>
                        <tr>
                            <th class="sticky-col">Employee</th>
                            @if($viewMode === 'weekly')
                                @foreach($weekDays as $wDay)
                                @php
                                    $wDate = $wDay->format('Y-m-d');
                                    $wDayNum = $wDay->day;
                                    $wDayKey = str_pad($wDayNum, 2, '0', STR_PAD_LEFT);
                                    $wMonth = $wDay->month;
                                    $wYear = $wDay->year;
                                    $isHolidayW = DB::table('holidays')->whereDate('holidaydate', $wDate)->exists();
                                    $isWeekendW = in_array($wDay->format('D'), ['Sat','Sun']);
                                    $isTodayW   = $wDate === date('Y-m-d');
                                @endphp
                                <th class="day-head {{ $isHolidayW ? 'is-holiday' : '' }} {{ $isWeekendW ? 'is-weekend' : '' }} {{ $isTodayW ? 'is-today' : '' }}">
                                    <span class="day-num">{{ $wDayNum }}</span>
                                    <span class="day-name">{{ $wDay->format('D') }}</span>
                                    @if($wMonth != $month) <span style="font-size:.52rem;color:var(--ink-4)">{{ $wDay->format('M') }}</span> @endif
                                    @if($isTodayW)<span class="today-dot"></span>@endif
                                </th>
                                @endforeach
                            @else
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                        $isCompanyHoliday = isset($holidays[str_pad($day, 2, '0', STR_PAD_LEFT)]);
                                        $dayName = date('D', strtotime($currentDate));
                                        $isWeekendCol = in_array($dayName, ['Sat', 'Sun']);
                                        $isToday = $currentDate === date('Y-m-d');
                                    @endphp
                                    <th class="day-head {{ $isCompanyHoliday ? 'is-holiday' : '' }} {{ $isWeekendCol ? 'is-weekend' : '' }} {{ $isToday ? 'is-today' : '' }}">
                                        <span class="day-num">{{ $day }}</span>
                                        <span class="day-name">{{ $dayName }}</span>
                                        @if($isCompanyHoliday)
                                            <span class="holiday-dot" data-bs-toggle="tooltip" title="Company Holiday"></span>
                                        @elseif($isToday)
                                            <span class="today-dot"></span>
                                        @endif
                                    </th>
                                @endfor
                            @endif
                            <th class="summary-head sticky-right">Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        @php
                        $presentCount = 0;
                        $partialCount = 0;
                        $lateCount = 0;
                        $employeeWorkingDays = 0;
                        $totalHours = 0;
                        $leaveCount = 0;

                        $employeeMonthLeaves = isset($employeeLeaves[$employee->id]) ? $employeeLeaves[$employee->id] : collect();
                        $empSchedules = $schedulesByEmployee->get($employee->id) ?? collect();
                        $findScheduleForDate = function($date) use ($empSchedules) {
                            foreach ($empSchedules as $sch) {
                                if ($sch->repeat_every_week) {
                                    if ($date >= $sch->schedule_start_date) return $sch;
                                } else {
                                    if ($date >= $sch->schedule_start_date && $date <= $sch->schedule_end_date) return $sch;
                                }
                            }
                            return null;
                        };
                        $isWorkingWeekday = function($date, $daysOfWeek) {
                            if (!$daysOfWeek) return false;
                            $tokens = array_map(fn($v) => strtolower(trim($v)), explode(',', $daysOfWeek));
                            $dayFull  = strtolower(date('l', strtotime($date)));
                            $dayShort = strtolower(date('D',  strtotime($date)));
                            $dayNum   = (string) date('N', strtotime($date));
                            $isSun = $dayNum === '7';
                            return in_array($dayFull,$tokens,true)||in_array($dayShort,$tokens,true)||in_array($dayNum,$tokens,true)||($isSun&&(in_array('0',$tokens,true)||in_array('7',$tokens,true)));
                        };
                        // Build day list for current view
                        $dayList = [];
                        if ($viewMode === 'weekly') {
                            foreach ($weekDays as $wDay) {
                                $dayList[] = [
                                    'date'    => $wDay->format('Y-m-d'),
                                    'day'     => $wDay->day,
                                    'dayKey'  => str_pad($wDay->day, 2, '0', STR_PAD_LEFT),
                                    'month'   => $wDay->month,
                                    'year'    => $wDay->year,
                                ];
                            }
                        } else {
                            for ($d = 1; $d <= $daysInMonth; $d++) {
                                $dayList[] = [
                                    'date'   => sprintf('%04d-%02d-%02d', $year, $month, $d),
                                    'day'    => $d,
                                    'dayKey' => str_pad($d, 2, '0', STR_PAD_LEFT),
                                    'month'  => $month,
                                    'year'   => $year,
                                ];
                            }
                        }
                        @endphp
                            <tr>
                                <td class="sticky-col">
                                    <div class="emp-cell">
                                        <div class="emp-avatar">
                                            <span>{{ strtoupper(substr($employee->firstname, 0, 1)) }}</span>
                                            <i class="emp-dot"></i>
                                        </div>
                                        <div class="emp-info">
                                            <div class="emp-id">#{{ $employee->id }}</div>
                                            <div class="emp-name">{{ $employee->firstname }} {{ $employee->lastname }}</div>
                                            <div class="emp-meta">
                                                @if($employee->shift_name)
                                                    <span class="chip chip-orange"><i class="fa-solid fa-clock" style="font-size:.55rem;margin-right:.2rem;"></i>{{ $employee->shift_name }}</span>
                                                @endif
                                                @if($employee->department)
                                                    <span class="chip chip-muted">{{ $employee->department }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($dayList as $dl)
                                @php
                                    $currentDate = $dl['date'];
                                    $dayKey      = $dl['dayKey'];
                                    $dlMonth     = $dl['month'];
                                    $dlYear      = $dl['year'];

                                    // attendance lookup — in weekly view cross-month days may differ
                                    if ($dlMonth == $month && $dlYear == $year) {
                                        $attendance = $attendanceData[$employee->id][$dayKey] ?? null;
                                    } else {
                                        $attendance = DB::table('attendances')
                                            ->where('employee_id', $employee->id)
                                            ->whereDate('date', $currentDate)
                                            ->first();
                                    }

                                    $companyHoliday = DB::table('holidays')->whereDate('holidaydate', $currentDate)->first();
                                    $isCompanyHoliday = !is_null($companyHoliday);

                                    $isEmployeeOnLeave = false;
                                    $employeeLeave = null;
                                    foreach ($employeeMonthLeaves as $leave) {
                                        if ($currentDate >= $leave->from_date && $currentDate <= $leave->to_date) {
                                            $isEmployeeOnLeave = true; $employeeLeave = $leave; break;
                                        }
                                    }

                                    $activeSchedule   = $findScheduleForDate($currentDate);
                                    $shouldWork       = false;
                                    $isEmployeeWeekend = false;

                                    if ($activeSchedule && !$isCompanyHoliday && !$isEmployeeOnLeave) {
                                        $shouldWork = $isWorkingWeekday($currentDate, $activeSchedule->days_of_week);
                                    }
                                    if ((!$activeSchedule || !$shouldWork) && !$isCompanyHoliday && !$isEmployeeOnLeave) {
                                        $isEmployeeWeekend = true;
                                    }
                                    if ($shouldWork && $currentDate <= date('Y-m-d')) { $employeeWorkingDays++; }
                                    if ($isEmployeeOnLeave) { $leaveCount++; }
                                @endphp
                                <td class="attendance-cell {{ $shouldWork ? 'working-day' : '' }} {{ $isEmployeeWeekend ? 'employee-weekend' : '' }} {{ $isEmployeeOnLeave ? 'employee-leave' : '' }}"
                                    data-date="{{ $currentDate }}" data-employee="{{ $employee->id }}">
                                    @if($isCompanyHoliday)
                                        <span class="att-pill pill-holiday" data-bs-toggle="tooltip"
                                              title="Holiday: {{ $companyHoliday->holidayname ?? '' }}">
                                            <i class="fa-solid fa-umbrella-beach"></i>
                                        </span>
                                    @elseif($isEmployeeOnLeave)
                                        <span class="att-pill pill-leave cursor-pointer" data-bs-toggle="tooltip"
                                              title="Leave: {{ ucfirst($employeeLeave->leave_type) }}"
                                              onclick="showLeaveDetails('{{ $employee->id }}', '{{ $currentDate }}')">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                        </span>
                                    @elseif($isEmployeeWeekend)
                                        <span class="att-dash"><i class="fa-solid fa-minus"></i></span>
                                    @elseif($attendance)
                                        @if($attendance->punch_in && $attendance->punch_out)
                                            @php $presentCount++; $totalHours += $attendance->working_hours ?? 0; if($attendance->status=='late') $lateCount++; @endphp
                                            <span class="att-pill pill-present cursor-pointer"
                                                  onclick="showAttendanceDetails('{{ $employee->id }}','{{ $currentDate }}')">
                                                <i class="fa-solid fa-check"></i>
                                                @if($attendance->status=='late')<i class="fa-solid fa-clock" style="font-size:.55rem;margin-left:1px"></i>@endif
                                            </span>
                                        @elseif($attendance->punch_in)
                                            @php $partialCount++; @endphp
                                            <span class="att-pill pill-partial cursor-pointer"
                                                  onclick="showAttendanceDetails('{{ $employee->id }}','{{ $currentDate }}')">
                                                <i class="fa-solid fa-exclamation"></i>
                                            </span>
                                        @endif
                                    @elseif($shouldWork)
                                        @if($currentDate <= date('Y-m-d'))
                                            <span class="att-pill pill-absent" data-bs-toggle="tooltip" title="Absent">
                                                <i class="fa-solid fa-xmark"></i>
                                            </span>
                                        @else
                                            <span class="att-dash"><i class="fa-solid fa-minus"></i></span>
                                        @endif
                                    @endif
                                </td>
                                @endforeach

                                @php
                                    $absent  = max(0, $employeeWorkingDays - $presentCount - $partialCount);
                                    $pct     = $employeeWorkingDays > 0 ? round(($presentCount / $employeeWorkingDays) * 100) : 0;
                                    $donutColor = $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#ff6a00' : '#dc2626');
                                    $hoursBarWidth = min(100, round(($totalHours / max(1, 200)) * 100));
                                @endphp
                                <td class="summary-cell sticky-right">
                                    <div class="sum-card">
                                        <div class="sum-donut-wrap">
                                            <svg class="sum-donut" viewBox="0 0 36 36">
                                                <circle class="donut-track" cx="18" cy="18" r="15.915" fill="none" stroke-width="3"/>
                                                <circle class="donut-fill" cx="18" cy="18" r="15.915" fill="none" stroke-width="3"
                                                    stroke="{{ $donutColor }}"
                                                    stroke-dasharray="{{ $pct }} {{ 100 - $pct }}"
                                                    stroke-dashoffset="25"/>
                                            </svg>
                                            <div class="sum-pct">
                                                <span class="sum-pct-num">{{ $pct }}</span>
                                                <span class="sum-pct-unit">%</span>
                                            </div>
                                        </div>
                                        <div class="sum-stats-row">
                                            <div class="ssi ssi-present" title="Present"><span class="ssi-val">{{ $presentCount }}</span><span class="ssi-lbl">Pres</span></div>
                                            <div class="ssi ssi-absent"  title="Absent" ><span class="ssi-val">{{ $absent }}</span><span class="ssi-lbl">Abs</span></div>
                                            <div class="ssi ssi-late"   title="Late"   ><span class="ssi-val">{{ $lateCount }}</span><span class="ssi-lbl">Late</span></div>
                                            <div class="ssi ssi-leave"  title="Leave"  ><span class="ssi-val">{{ $leaveCount }}</span><span class="ssi-lbl">Lv</span></div>
                                        </div>
                                        <div class="sum-hours-row">
                                            <i class="fa-regular fa-clock"></i>
                                            <span class="sum-hours-val">{{ number_format($totalHours, 1) }}h</span>
                                            <div class="sum-hours-bar"><div class="sum-hours-bar-fill" style="width:{{ $hoursBarWidth }}%"></div></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===== MOBILE CARD VIEW ===== --}}
            <div class="mobile-view">
                @foreach($employees as $employee)
                @php
                    $mPresent = 0; $mPartial = 0; $mLate = 0; $mAbsent = 0; $mLeave = 0; $mHours = 0; $mWorkDays = 0;
                    $employeeMonthLeaves = isset($employeeLeaves[$employee->id]) ? $employeeLeaves[$employee->id] : collect();
                    $empSchedules = $schedulesByEmployee->get($employee->id) ?? collect();
                    $findSched2 = function($date) use ($empSchedules) {
                        foreach ($empSchedules as $sch) {
                            if ($sch->repeat_every_week) { if ($date >= $sch->schedule_start_date) return $sch; }
                            else { if ($date >= $sch->schedule_start_date && $date <= $sch->schedule_end_date) return $sch; }
                        } return null;
                    };
                    $isWorkDay2 = function($date, $dow) {
                        if (!$dow) return false;
                        $t = array_map(fn($v) => strtolower(trim($v)), explode(',', $dow));
                        $f = strtolower(date('l',strtotime($date))); $s = strtolower(date('D',strtotime($date)));
                        $n = (string)date('N',strtotime($date)); $sun = $n==='7';
                        return in_array($f,$t)||in_array($s,$t)||in_array($n,$t)||($sun&&(in_array('0',$t)||in_array('7',$t)));
                    };
                    // build mobile day list same as desktop
                    $mDayList = [];
                    if ($viewMode === 'weekly') {
                        foreach ($weekDays as $wDay) {
                            $mDayList[] = ['date'=>$wDay->format('Y-m-d'),'day'=>$wDay->day,'label'=>$wDay->format('D d'),'month'=>$wDay->month,'year'=>$wDay->year];
                        }
                    } else {
                        for ($d=1;$d<=$daysInMonth;$d++) {
                            $dt = sprintf('%04d-%02d-%02d',$year,$month,$d);
                            $mDayList[] = ['date'=>$dt,'day'=>$d,'label'=>date('D d',strtotime($dt)),'month'=>$month,'year'=>$year];
                        }
                    }
                    // compute summary stats for this employee
                    $mDayStatuses = [];
                    foreach ($mDayList as $md) {
                        $mDate = $md['date'];
                        $mDK   = str_pad($md['day'],2,'0',STR_PAD_LEFT);
                        if ($md['month']==$month && $md['year']==$year) {
                            $mAtt = $attendanceData[$employee->id][$mDK] ?? null;
                        } else {
                            $mAtt = DB::table('attendances')->where('employee_id',$employee->id)->whereDate('date',$mDate)->first();
                        }
                        $isHol = DB::table('holidays')->whereDate('holidaydate',$mDate)->exists();
                        $isLv  = false;
                        foreach ($employeeMonthLeaves as $lv) { if ($mDate>=$lv->from_date&&$mDate<=$lv->to_date){$isLv=true;break;} }
                        $sch   = $findSched2($mDate);
                        $work  = $sch && !$isHol && !$isLv && $isWorkDay2($mDate,$sch->days_of_week);
                        $status = 'off';
                        if ($isHol) $status = 'holiday';
                        elseif ($isLv) { $status = 'leave'; $mLeave++; }
                        elseif (!$work) $status = 'off';
                        elseif ($mAtt && $mAtt->punch_in && $mAtt->punch_out) {
                            $status = $mAtt->status=='late'?'late':'present';
                            if ($status==='late') $mLate++; else $mPresent++;
                            $mHours += $mAtt->working_hours ?? 0;
                            if ($mDate<=date('Y-m-d')) $mWorkDays++;
                        } elseif ($mAtt && $mAtt->punch_in) {
                            $status = 'partial'; $mPartial++;
                            if ($mDate<=date('Y-m-d')) $mWorkDays++;
                        } elseif ($work && $mDate<=date('Y-m-d')) {
                            $status = 'absent'; $mAbsent++; $mWorkDays++;
                        } elseif ($work) $status = 'future';
                        $mDayStatuses[] = array_merge($md, ['status'=>$status]);
                    }
                    $totalMWork = $mPresent + $mPartial + $mAbsent;
                    $mPct = $totalMWork > 0 ? round($mPresent/$totalMWork*100) : 0;
                @endphp
                <div class="mob-card">
                    <div class="mob-card-header">
                        <div class="emp-avatar" style="width:38px;height:38px;border-radius:10px;font-size:.85rem">
                            <span>{{ strtoupper(substr($employee->firstname,0,1)) }}</span>
                        </div>
                        <div class="mob-emp-info">
                            <div class="mob-emp-name">{{ $employee->firstname }} {{ $employee->lastname }}</div>
                            <div class="mob-emp-sub">{{ $employee->department ?? '' }} @if($employee->shift_name) &bull; {{ $employee->shift_name }} @endif</div>
                        </div>
                        <div class="mob-pct" style="color:{{ $mPct>=80?'#16a34a':($mPct>=50?'#ff6a00':'#dc2626') }}">{{ $mPct }}%</div>
                    </div>
                    <div class="mob-days">
                        @foreach($mDayStatuses as $mds)
                        @php
                            $pillClass = match($mds['status']) {
                                'present' => 'pill-present',
                                'late'    => 'pill-partial',
                                'partial' => 'pill-partial',
                                'absent'  => 'pill-absent',
                                'leave'   => 'pill-leave',
                                'holiday' => 'pill-holiday',
                                default   => ''
                            };
                            $pillIcon = match($mds['status']) {
                                'present' => 'fa-check',
                                'late','partial' => 'fa-exclamation',
                                'absent'  => 'fa-xmark',
                                'leave'   => 'fa-calendar-xmark',
                                'holiday' => 'fa-umbrella-beach',
                                default   => 'fa-minus'
                            };
                        @endphp
                        <div class="mob-day">
                            <span class="mob-day-label">{{ $mds['label'] }}</span>
                            @if($mds['status'] === 'off' || $mds['status'] === 'future')
                                <span class="att-dash" style="width:28px;height:28px"><i class="fa-solid fa-minus"></i></span>
                            @else
                                <span class="att-pill {{ $pillClass }}" style="width:28px;height:28px;font-size:.7rem">
                                    <i class="fa-solid {{ $pillIcon }}"></i>
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="mob-stats">
                        <div class="mob-stat"><span class="mob-stat-val" style="color:#16a34a">{{ $mPresent }}</span><span class="mob-stat-lbl">Pres</span></div>
                        <div class="mob-stat"><span class="mob-stat-val" style="color:#dc2626">{{ $mAbsent }}</span><span class="mob-stat-lbl">Abs</span></div>
                        <div class="mob-stat"><span class="mob-stat-val" style="color:#b45309">{{ $mLate }}</span><span class="mob-stat-lbl">Late</span></div>
                        <div class="mob-stat"><span class="mob-stat-val" style="color:#2563eb">{{ $mLeave }}</span><span class="mob-stat-lbl">Leave</span></div>
                        <div class="mob-stat"><span class="mob-stat-val" style="color:#ff6a00">{{ number_format($mHours,1) }}h</span><span class="mob-stat-lbl">Hours</span></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

{{-- ============================== MODALS ============================== --}}
<div class="modal fade att-modal" id="employeeLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-calendar-plus"></i> Add Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="employeeLeaveForm">
                    <div class="mb-3">
                        <label class="form-label att-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select att-input" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label att-label">From Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control att-input" name="from_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label att-label">To Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control att-input" name="to_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label att-label">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-select att-input" name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            <option value="personal">Personal Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="vacation">Vacation</option>
                            <option value="emergency">Emergency</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label att-label">Reason</label>
                        <textarea class="form-control att-input" name="leave_reason" rows="3" placeholder="Reason for leave (optional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="att-btn att-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="att-btn att-btn-solid" onclick="saveEmployeeLeave()">
                    <i class="fa-solid fa-save"></i> Add Leave
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade att-modal" id="attendanceDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-circle-info"></i> Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attendanceDetailsContent"></div>
        </div>
    </div>
</div>

<div class="modal fade att-modal" id="exportProgressModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-orange" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Preparing export...</p>
                <small class="text-muted">This may take a few moments</small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade att-modal" id="manualAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-user-clock"></i> Mark Manual Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="manualAttendanceForm">
                    <input type="hidden" name="employee_id" id="manual_employee_id">
                    <input type="hidden" name="date" id="manual_date">

                    <div class="mb-3">
                        <label class="form-label att-label">Employee</label>
                        <input type="text" class="form-control att-input" id="manual_employee_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label att-label">Date</label>
                        <input type="text" class="form-control att-input" id="manual_date_display" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label att-label">Punch In Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control att-input" name="punch_in" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label att-label">Punch Out Time</label>
                                <input type="time" class="form-control att-input" name="punch_out">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label att-label">Status</label>
                        <select class="form-select att-input" name="status">
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label att-label">Notes</label>
                        <textarea class="form-control att-input" name="notes" rows="2" placeholder="Additional notes (optional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="att-btn att-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="att-btn att-btn-solid" onclick="saveManualAttendance()">
                    <i class="fa-solid fa-save"></i> Mark Attendance
                </button>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip({ placement: 'top', html: true });

        $('input[name="from_date"]').change(function() {
            const fromDate = $(this).val();
            if (fromDate) {
                $('input[name="to_date"]').attr('min', fromDate);
                if (!$('input[name="to_date"]').val()) {
                    $('input[name="to_date"]').val(fromDate);
                }
            }
        });

        $('#employeeLeaveForm').on('submit', function(e) {
            e.preventDefault();
            saveEmployeeLeave();
        });
    });

    function showLeaveModal() { $('#employeeLeaveModal').modal('show'); }

    function saveEmployeeLeave() {
        const form = document.getElementById('employeeLeaveForm');
        const formData = new FormData(form);

        if (!formData.get('employee_id') || !formData.get('from_date') || !formData.get('to_date') || !formData.get('leave_type')) {
            Swal.fire({ title: 'Validation Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
            return;
        }

        const fromDate = new Date(formData.get('from_date'));
        const toDate = new Date(formData.get('to_date'));
        if (toDate < fromDate) {
            Swal.fire({ title: 'Invalid Date Range', text: 'To date must be after or equal to from date.', icon: 'error', confirmButtonText: 'OK' });
            return;
        }

        Swal.fire({ title: 'Adding Leave...', text: 'Please wait while we process your request.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.post('/admin/attendance/add-leave', {
            employee_id: formData.get('employee_id'),
            from_date: formData.get('from_date'),
            to_date: formData.get('to_date'),
            leave_type: formData.get('leave_type'),
            leave_reason: formData.get('leave_reason'),
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                Swal.fire({ title: 'Success!', text: 'Employee leave added successfully.', icon: 'success', timer: 2000, showConfirmButton: false })
                    .then(() => { $('#employeeLeaveModal').modal('hide'); location.reload(); });
            } else {
                Swal.fire({ title: 'Error!', text: response.message || 'Failed to add leave.', icon: 'error', confirmButtonText: 'OK' });
            }
        }).fail(function(xhr) {
            let errorMessage = 'Failed to add employee leave.';
            if (xhr.responseJSON && xhr.responseJSON.message) { errorMessage = xhr.responseJSON.message; }
            Swal.fire({ title: 'Error!', text: errorMessage, icon: 'error', confirmButtonText: 'OK' });
        });
    }

    function showLeaveDetails(employeeId, date) {
        $.get(`/admin/attendance/leave-details/${employeeId}/${date}`, function(data) {
            $('#attendanceDetailsContent').html(data);
            $('#attendanceDetailsModal').modal('show');
        }).fail(function() {
            Swal.fire({ title: 'Error!', text: 'Failed to load leave details.', icon: 'error', confirmButtonText: 'OK' });
        });
    }

    function showAttendanceDetails(employeeId, date) {
        $.get(`/admin/attendance/details/${employeeId}/${date}`, function(data) {
            $('#attendanceDetailsContent').html(data);
            $('#attendanceDetailsModal').modal('show');
        }).fail(function() {
            Swal.fire({ title: 'Error!', text: 'Failed to load attendance details.', icon: 'error', confirmButtonText: 'OK' });
        });
    }

    function markAttendance(employeeId, date) {
        const employeeName = $(`td[data-employee="${employeeId}"]`).closest('tr').find('h6').text();

        $('#manual_employee_id').val(employeeId);
        $('#manual_date').val(date);
        $('#manual_employee_name').val(employeeName);
        $('#manual_date_display').val(new Date(date).toLocaleDateString());

        document.getElementById('manualAttendanceForm').reset();
        $('#manual_employee_id').val(employeeId);
        $('#manual_date').val(date);
        $('#manual_employee_name').val(employeeName);
        $('#manual_date_display').val(new Date(date).toLocaleDateString());

        $('#manualAttendanceModal').modal('show');
    }

    function saveManualAttendance() {
        const form = document.getElementById('manualAttendanceForm');
        const formData = new FormData(form);

        if (!formData.get('punch_in')) {
            Swal.fire({ title: 'Validation Error', text: 'Punch in time is required.', icon: 'error', confirmButtonText: 'OK' });
            return;
        }

        Swal.fire({ title: 'Marking Attendance...', text: 'Please wait while we process your request.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.post('/admin/attendance/mark-manual', {
            employee_id: formData.get('employee_id'),
            date: formData.get('date'),
            punch_in: formData.get('punch_in'),
            punch_out: formData.get('punch_out'),
            status: formData.get('status'),
            notes: formData.get('notes'),
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                Swal.fire({ title: 'Success!', text: 'Attendance marked successfully.', icon: 'success', timer: 2000, showConfirmButton: false })
                    .then(() => { $('#manualAttendanceModal').modal('hide'); location.reload(); });
            } else {
                Swal.fire({ title: 'Error!', text: response.message || 'Failed to mark attendance.', icon: 'error', confirmButtonText: 'OK' });
            }
        }).fail(function(xhr) {
            let errorMessage = 'Failed to mark attendance.';
            if (xhr.responseJSON && xhr.responseJSON.message) { errorMessage = xhr.responseJSON.message; }
            Swal.fire({ title: 'Error!', text: errorMessage, icon: 'error', confirmButtonText: 'OK' });
        });
    }
    const attendanceExportRoute = "{{ route('admin.attendance.export') }}";

    function switchView(mode) {
        document.getElementById('view_mode').value = mode;
        document.getElementById('attendanceFilterForm').submit();
    }

    function changeWeek(delta, resetToday = false) {
        const current = parseInt(document.getElementById('week_offset').value) || 0;
        document.getElementById('week_offset').value = resetToday ? 0 : current + delta;
        document.getElementById('view_mode').value = 'weekly';
        document.getElementById('attendanceFilterForm').submit();
    }

    function exportAttendance(format = 'excel') {
        const month = $('#month').val();
        const year = $('#year').val();
        const department = $('#department_id').val();

        $('#exportProgressModal').modal('show');

        const params = new URLSearchParams({ month, year, format });
        if (department) { params.append('department_id', department); }

        const downloadUrl = `${attendanceExportRoute}?${params.toString()}`;

        const link = document.createElement('a');
        link.href = downloadUrl;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        setTimeout(() => { $('#exportProgressModal').modal('hide'); }, 2000);
    }
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.cmd-field select').select2({
            minimumResultsForSearch: 10,
            width: '100%'
        });
    });
</script>
<style>
/* Custom Select2 styling to match cmd-field */
.cmd-field .select2-container--default .select2-selection--single {
    background-color: transparent;
    border: none;
    height: 100%;
    display: flex;
    align-items: center;
}
.cmd-field .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--ink);
    font-weight: 600;
    font-size: .88rem;
    padding-left: 0;
}
.cmd-field .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    right: 5px;
    display: flex;
    align-items: center;
}
.select2-dropdown {
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    box-shadow: var(--sh-md);
    padding: 4px;
    border-top: 1px solid var(--line) !important;
}
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: var(--o-soft);
    color: var(--o);
    border-radius: 6px;
}
.select2-container--default .select2-results__option {
    border-radius: 6px;
    margin-bottom: 2px;
    font-size: .85rem;
    font-weight: 600;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
    padding: 6px 8px;
    font-size: .85rem;
}
</style>
@endsection