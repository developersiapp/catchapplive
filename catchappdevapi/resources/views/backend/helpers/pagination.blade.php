<div class="pagination-outer">
    <div class="">
        <ul class="pagination js-pagination">
            <?php
            $totalPages = ceil($totalRecords / $perPage);
            $currentPage = $offset / $perPage;
            $doffset = 0;
            $showingFirstRecord = $offset + 1;
            $showingLastRecord = $offset + $perPage;
            $prevOffset = $offset - $perPage;
            if ($prevOffset < 0) {
                $prevOffset = 0;
            }
            if ($showingFirstRecord > $totalRecords) {
                $showingFirstRecord = $totalRecords;
            }

            if ($showingLastRecord > $totalRecords) {
                $showingLastRecord = $totalRecords;
            }

            $nextOffset = $offset + $perPage;
            if ($nextOffset > $totalRecords) {
                $nextOffset = $offset;
            }
            $lastOffset = ($totalPages - 1) * $perPage;

            ?>
            <li class="{{$offset == 0?'disabled':''}}"><a href="#" data-offset="{{0}}">&laquo; &laquo;</a></li>
            <li class="{{$offset == 0?'disabled':''}}"><a href="#" data-offset="{{$prevOffset}}">&laquo;</a></li>
            @for($i=1; $i<=$totalPages; $i++)
                <?php
                $cls = abs($currentPage - $i) > 5 ? 'hidden' : '';
                $doffset = ($i - 1) * $perPage;
                ?>
                @if($cls == 'hidden')

                @else
                    <li class="{{$cls}} {{$doffset == $offset?'active':''}}"><a href="#"
                                                                                data-offset="{{$doffset}}"><?=$i?></a>
                    </li>
                @endif
            @endfor
            <li class="{{$offset == $doffset?'disabled':''}}"><a href="#" data-offset="{{$nextOffset}}">&raquo;</a></li>
            <li class="{{$offset == $doffset?'disabled':''}}"><a href="#" data-offset="{{$lastOffset}}">&raquo;
                    &raquo;</a>
            </li>
        </ul>
    </div>
   <!-- <div class="">
        <select class="form-control pagination-select">
            <option value="">Jump To Page</option>
            @for($i=1; $i<=$totalPages; $i++)
                <?php
                $doffset = ($i - 1) * $perPage;
                ?>
                <option value="{{$doffset}}">
                    <?=$i?>
                </option>
            @endfor
        </select>
    </div> -->
    <div class="">
        Showing {{$showingFirstRecord}} - {{$showingLastRecord}} of
        {{$totalRecords}} Total Records
    </div>

</div>
<style>
    .pagination.js-pagination {
        margin: 0;
    }

    .pagination-outer > div {
        display: inline-block;
        float: left;
        margin: 0 10px;
    }
</style>
