<?php
    namespace phpcore;

    final class EntryLifetime extends Enum
    {
        const Singleton = 1;
        const Transient = 2;
    }
?>