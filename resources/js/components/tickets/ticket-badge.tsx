import { Badge } from '@/components/ui/badge';
import { badgeClassFor } from '@/lib/ticket-format';
import { type TicketEnumValue } from '@/types/tickets';

interface TicketBadgeProps {
    kind: 'status' | 'priority';
    value: TicketEnumValue;
}

export function TicketBadge({ kind, value }: TicketBadgeProps) {
    return (
        <Badge variant="outline" className={badgeClassFor(kind, value.value)}>
            {value.label}
        </Badge>
    );
}
